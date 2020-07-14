import Path from 'path';
import _ from 'lodash';
import scandir from 'sb-scandir';
import deepmerge from "../framework/Merge";

const outputFileSync = require('output-file-sync');
const {readFileSync, existsSync} = require('fs');

const EXTENSION_PATH = 'src/extension/';
const IMPORT_LINES = 'IMPORT_LINES';
const MODULE_LINES = 'MODULE_LINES';

/**
 * Scan all extension's config files to update src/extension/config.js
 */
scandir(EXTENSION_PATH, true, function (path) {
    const baseName = Path.basename(path);
    return path !== `${EXTENSION_PATH}config.js` &&
        (baseName === 'config.js' || baseName.indexOf('.') === -1)
}).then(function (result) {
    console.log('Updating modules.json file...');
    let moduleStatus = updateModuleFile(
        result.files.map(file => file.replace(EXTENSION_PATH, '').replace('/etc/config.js', ''))
    );

    // Module lines
    let files = result.files.filter(file => {
        let moduleName = file.replace(EXTENSION_PATH, '').replace('/etc/config.js', '');
        return moduleStatus[moduleName];
    }).sort();

    let moduleLines = files.map((file, index) => {
        let moduleName = file.replace(EXTENSION_PATH, '').replace('/etc/config.js', '');
        return `__${index}_${_.snakeCase(moduleName)}Config`;
    });

    let importLines = files.map((file, index) => {
        const pathFile = file.replace(EXTENSION_PATH, '');
        return `import ${moduleLines[index]} from './${pathFile}';`;
    });

    console.log('Updating config.js file...');
    let tabs = '        ';
    let configFile = readFileSync(`${EXTENSION_PATH}config.js`, 'utf-8').replace(
        new RegExp(`${IMPORT_LINES}[\\s\\S]*${IMPORT_LINES}`, 'g'),
        `${IMPORT_LINES}\n` + importLines.concat([`// ${IMPORT_LINES}`]).join('\n')
    ).replace(
        new RegExp(`${MODULE_LINES}[\\s\\S]*${MODULE_LINES}`, 'g'),
        `${MODULE_LINES}\n${tabs}` + moduleLines.concat([`// ${MODULE_LINES}`]).join(`,\n${tabs}`)
    );

    outputFileSync(`${EXTENSION_PATH}/config.js`, configFile);

    // Update package.json
    updatePackage(files);
});

/**
 * Generate module.json file
 *
 * @param {Array} modules
 * @returns {Object}
 */
function updateModuleFile(modules) {
    // New module content
    let content = {};
    modules.forEach(module => {
        content[module] = 1;
    });
    // Merge with file content
    content = deepmerge(content, JSON.parse(readFileSync(`${EXTENSION_PATH}/modules.json`, 'utf-8')));
    outputFileSync(`${EXTENSION_PATH}/modules.json`, JSON.stringify(content, null, 2) + '\n', 'utf-8');
    return content;
}

/**
 * Update package.json file and install new required node modules
 *
 * @param {Array} files
 */
function updatePackage(files) {
    console.log('Updating package.json file...');
    let packageJson = JSON.parse(readFileSync('package.json', 'utf-8'));
    files.forEach(filePath => {
        filePath = filePath.replace('/etc/config.js', '/package.json');
        if ('package.json' === Path.basename(filePath) && existsSync(filePath)) {
            packageJson = deepmerge(packageJson, JSON.parse(readFileSync(filePath, 'utf-8')));
        }
    });
    outputFileSync('package.json', JSON.stringify(packageJson, null, 2) + '\n', 'utf-8');

    console.log('Installing modules...');
    require('child_process').exec('npm install', (error, stdout, stderr) => {
        if (error) {
            console.error(`exec error: ${error}`);
            return;
        }
        console.log(`stdout: ${stdout}`);
        console.log(`stderr: ${stderr}`);
    });
}
