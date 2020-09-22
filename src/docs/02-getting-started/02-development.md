# Fractal

To start `Fractal`, you'll need to run the following command:

```bash
yarn gulp
```

This will create a local `Fractal server` instance available at http://localhost:3000 _(unless port 3000 is taken)_.

Full list of available `Fractal` commands:

```
# start fractal in development mode
yarn gulp

# build static assets only
yarn build

# export fractal to static files
yarn fractal:build

# build static assets & export fractal to static files
yarn build:all
```

## Fractal Build

Using `yarn build` will export `Fractal` to static files, exported to `static-html`.

This is useful for seeing the actual HTML output for each page, or just having a reference to components without having to start the `Fractal` server.

[yarn]: https://classic.yarnpkg.com/en/
[node]: https://nodejs.org/en/
[fractal]: https://fractal.build/
[fractal-docs]: https://fractal.build/guide/

# Accessibility

```bash
# cd into project directory
cd [project-directory]

# run axe
yarn axe

# run axe for a specific template
AXE_FILTER=home yarn axe
```
