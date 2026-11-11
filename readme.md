# Developer.WordPress.org Theme

## Development

### Prerequisites

* Docker
* Node/npm
* Composer

### Setup

1. Set up repo dependencies.

    ```bash
    npm run setup:tools
    ```

1. Build the assets. If there's anything that needs setting up.

    ```bash
    npm run build
    ```

1. Start the local environment.

    ```bash
    npm run wp-env start
    ```

1. Run the setup script.

    ```bash
    npm run setup
    ```

1. Visit site at [localhost:8888](http://localhost:8888).

1. Log in with username `admin` and password `password`.

### WordPress Playground

To run the environment without Docker, use WordPress Playground:

```bash
npm run playground
```

The Playground setup mounts the same WordPress.org sandbox bootstrap used by `wp-env`, activates the local plugins and theme, and creates the starter pages.

The `playground:import-docs` command starts Playground after importing the manifest and Markdown content for a documentation post type. For example, to import the Playground documentation, run:

```bash
IMPORT_POST_TYPE=playground npm run playground:import-docs
```

### Environment management

These must be run in the project's root folder, _not_ in theme/plugin subfolders.

* Stop the environment.

    ```bash
    npm run wp-env stop
    ```

* Restart the environment.

    ```bash
    npm run wp-env start
    ```

* Open a shell inside the docker container.

    ```bash
    npm run wp-env run wordpress bash
    ```

* Run wp-cli commands. Keep the wp-cli command in quotes so that the flags are passed correctly.

    ```bash
    npm run wp-env run cli "post list --post_status=publish"
    ```

* Update composer dependencies and sync any `repo-tools` changes.

    ```bash
    npm run update:tools
    ```

* Watch for SCSS changes and rebuild the CSS as needed.

    ```bash
    npm run start:theme
    ```

* Parse the code reference again. This is run as part of the project setup.

    ```bash
    npm run parse
    ```

### Asset management

* Build assets once: `npm run build:theme`
* Watch assets and build on changes: `npm run start:theme`
