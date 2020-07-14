import GLOBAL_VARIABLES, {getPermission} from '../Config'

it('Get global variables', () => {
    expect(GLOBAL_VARIABLES).not.toBeNull();
    expect(GLOBAL_VARIABLES).not.toBeUndefined();
});

it('Check get permission function', () => {
    // Prepare environment
    let oldConfig = GLOBAL_VARIABLES.config;

    // Run test cases
    delete GLOBAL_VARIABLES.config;
    expect(getPermission()).toEqual([]);
    GLOBAL_VARIABLES.config = {};
    expect(getPermission()).toEqual([]);
    GLOBAL_VARIABLES.config.permissions = [];
    expect(getPermission()).toEqual([]);
    GLOBAL_VARIABLES.config.permissions = [''];
    expect(getPermission()).toEqual(['']);

    // Rollback environment
    GLOBAL_VARIABLES.config = oldConfig;
});
