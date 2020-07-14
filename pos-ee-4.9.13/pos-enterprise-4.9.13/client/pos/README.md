# PWA POS Client version 1.1.1

## Local Develop PWA POS App
1. `cp .env.development .env.development.local`
2. Edit `.env.development.local` file to fit your environment
3. `npm start`

## Run Unit Test
Run unit test for project
```
npm run test
```

Generate Code Coverage
```
npm run test -- --coverage
```

## Build with plugins

1. Install nodejs module with plugins `npm run upgrade`
2. Edit file `client/pos/src/extension/modules.json` to enable/disable module. If you change the content of this file, please rerun `npm run upgrade`
3. Build package with command `npm run build`

## Docs
1. [Event](docs/Event.md)
2. [Rewrite](docs/Rewrite.md)
3. [Plugin](docs/Plugin.md)
4. [Layout](docs/Layout.md)
5. [Component And Reducer](docs/ComponentReducer.md)
6. [Mixin](docs/Mixin.md)
