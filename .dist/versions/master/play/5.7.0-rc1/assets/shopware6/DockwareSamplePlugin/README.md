# Dockware Sample Plugin

This is our sample plugin to get you started with Shopware development
as fast as possible.

It's already installed and activated.
It also comes with a sample unit test and everything else you need.

## Concept
The sample plugin has its own vendors to allow you a plug and play installation
for non-development Shopware shops. Just upload and enjoy!

For this you might need production dependencies.
For development purpose on the other hand, you might want some dev-only-dependencies.
And for this we've created some make commands.
 

## Deliver Plugin
Please run the following command before delivering the plugin.
It will install only production dependencies for a plug'n'play installation.

```ruby
make prod
```

## Unit Tests
If you want to run the unit tests, start by installing all
dev-dependencies and then run the command.

```ruby
make dev
make test
```

## Static Analyzer
If you want to run a separate static analyzer for this plugin,
install the dev-dependencies and run this command.

```ruby
make dev
make stan
```