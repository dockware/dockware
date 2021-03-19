
## Easy Testing

Running Cypress tests is easy!
Just install it by using the built-in makefile commands
and either open your tests in the Cypress UI, or run them directly from your CLI.


### Installation

This folder contains a `makefile` with all required commands.
Run the installation command to install Cypress and all its dependencies on your machine

```ruby 
make install
```


### Cypress UI
If you want to run your Cypress UI, just open it with the following command.
Please note, because this is an Open Source project, we cannot include a
shop URL in the configuration. Thus you need to provide it on your own.

```ruby 
make open-ui url=http://localhost
```

### Run in CLI
You can also use the CLI command to run Cypress on your machine or directly
in your build pipeline.

```ruby 
make run url=http://localhost
```

