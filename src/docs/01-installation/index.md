# Installation

## System Requirements

Make sure these dependencies are on your local computer:

- [Node][node] - `10.x` or higher; `12.x` is recommended
- [Yarn][yarn] - `1.x`

## Git Repository

Clone the repository and intall dependencies:

```bash
# cd into Code directory; i.e. ~/Code
cd [code-directory]

# clone the repository
git clone git@github.com:jzhai-github/jtcc-fastspot.git

# cd into project
cd jtcc-fastspot

# install dependencies; we recommend using yarn
yarn install
```

## Fractal

[Fractal][fractal] is a tool to help you **build** and **document** web component libraries, and then **integrate** them into your projects.

## Helpful Links

- [Fractal Documentation][fractal-docs]

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

### Fractal Build

Using `yarn build` will export `Fractal` to static files, exported to `static-html`.

This is useful for seeing the actual HTML output for each page, or just having a reference to components without having to start the `Fractal` server.

[yarn]: https://classic.yarnpkg.com/en/
[node]: https://nodejs.org/en/
[fractal]: https://fractal.build/
[fractal-docs]: https://fractal.build/guide/
