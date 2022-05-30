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
    yarn workspaces run build
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

* Parse the code reference again. This is run as part of the project setup.

    ```bash
    yarn parse
    ```

### Asset management

* Build assets once: `yarn workspace wporg-parent-2021 build`
* Watch assets and build on changes: `yarn workspace wporg-parent-2021 start`
