# Developer.WordPress.org Theme

## Development

### Prerequisites

* Docker
* Node/npm
* Yarn
* Composer

### Setup

1. Set up repo dependencies.

    ```bash
    yarn setup:tools
    ```

1. Build the assets. If there's anything that needs setting up.

    ```bash
    yarn build
    ```

1. Start the local environment.

    ```bash
    yarn wp-env start
    ```

1. Run the setup script.

    ```bash
    yarn setup
    ```

1. Visit site at [localhost:8888](http://localhost:8888).

1. Log in with username `admin` and password `password`.

### WordPress Playground

To run the environment without Docker, use WordPress Playground:

```bash
yarn playground
```

The Playground setup mounts the same WordPress.org sandbox bootstrap used by `wp-env`, activates the local plugins and theme, and creates the starter pages.

To also import the Playground documentation, run:

```bash
yarn playground:import-docs
```

This uses `jq` to append a temporary import step to the base blueprint. The step runs the Playground importer through its WordPress actions after the theme has loaded.

To import from a different branch of the WordPress Playground repository, set `PLAYGROUND_BRANCH`:

```bash
PLAYGROUND_BRANCH=adding-handbook-manifest yarn playground:import-docs
```

### Environment management

These must be run in the project's root folder, _not_ in theme/plugin subfolders.

* Stop the environment.

    ```bash
    yarn wp-env stop
    ```

* Restart the environment.

    ```bash
    yarn wp-env start
    ```

* Open a shell inside the docker container.

    ```bash
    yarn wp-env run wordpress bash
    ```

* Run wp-cli commands. Keep the wp-cli command in quotes so that the flags are passed correctly.

    ```bash
    yarn wp-env run cli "post list --post_status=publish"
    ```

* Update composer dependencies and sync any `repo-tools` changes.

    ```bash
    yarn update:tools
    ```

* Watch for SCSS changes and rebuild the CSS as needed.

    ```bash
    yarn start:theme
    ```

* Parse the code reference again. This is run as part of the project setup.

    ```bash
    yarn parse
    ```

### Asset management

* Build assets once: `yarn build:theme`
* Watch assets and build on changes: `yarn start:theme`
