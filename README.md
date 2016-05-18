TYPO3 RPC: Remote Procedure Calls for TYPO3
===========================================

Exposes TYPO3 procedures as Tasks and accepts and responds to RPC queries sent by clients. Uses a Request/Response form
of communication where a very simple Request contains arguments for the task and a more complex Response allows for
returning not just standard responses but also definitions for form fields that the client displays when asking the user
to fill the arguments the task needs.

This also allows the RPC request/response to be performed over multiple steps. For example, the server can deliver a
response that asks for one variable to be filled and then sends that to the server which returns a new response based
on that variable. Until all arguments are filled and the task can be executed.

The RPC extension has OSX and iOS clients compatible with any Mac (el Capitan and up) and iPhone/iPad. The iOS versions
are still being developed and are not yet released. Once all clients are completed a release to the official App store
is planned; until then, the OSX client is available for download as a DMG archive (see below).

Installation
------------

Pull as composer dependency using `composer require namelesscoder/rpc` on the TYPO3 site that needs RPC.

Download and install the OSX or iOS application "TYPO3 RPC Client".

* [OSX Application (DMG)](https://github.com/NamelessCoder/rpc-client-osx/releases/download/1.0.0/TYPO3RPC-OSX-1.0.0.dmg).
  Mount, copy the application and first time you run it, right-click and select "Open" to acknowledge running the
  unsigned application (until distribution happens through App Store, then the apps will be signed)
* iOS Application (coming soon)

Or install the extension and use the backend module from a client TYPO3 site to connect to a TYPO3 server host.

How it works
------------

**If you are the systems administrator:**

1. You install the extension which automatically makes the HTTP endpoint available to clients
2. Clients connect to your site using the hostname of the site
3. When a client first connects he has no token - one gets created for him and the client stores/reports the token to
   the user. The token is created in the root system folder (pid zero) in TYPO3 as a record, inactive, locked to the
   client IP until validated.
4. User now reports his token to you and you locate the token record.
5. Edit the token record, switch on the "Has access?" toggle and assign desired access to tasks.
6. Client can now (reconnect and) execute the accessible tasks.

**If you are a user of the RPC client**

1. Open the client (app or backend module) and create a new connection
2. Enter as hostname the hostname of the site you wish to connect to
3. Press the "connect" icon/button
4. If it is the very first time you connect, a token is requested and reported to you. You then report this token to the
   systems administrator (usually a couple of characters is enough to identify it) and the systems administrator grants
   you the access you need. Reconnect when your token is validated.
5. If you have access, a list of tasks is presented to you. Click a task to begin executing it and the client app/module
   will show you form fields and feedback along the way.

Configuration
-------------

A collection of Tasks is included and can be configured via a PHP API:

```php
\NamelessCoder\Rpc\Manager\TaskManager::getInstance()
    ->getTaskById('help')
	->getTaskConfiguration()
	->setEnabled(FALSE)
```

And new custom Tasks can be added and manipulated via the same API:

```php
\NamelessCoder\Rpc\Manager\TaskManager::getInstance()->addTask(
    new \MyNamespace\MyExtension\MyCustomTask('my-custom-task')
);

\NamelessCoder\Rpc\Manager\TaskManager::getInstance()
    ->getTaskById('my-custom-task')
    ->doSomethingToTask();
```

The built-in tasks are:

* `list` which must be available if the RPC is going to be used from a client where tasks are listed before they are
  executed. Without this task, the client *must know the task ID that is going to be called without receiving it from
  the server*. The OSX/iOS clients do not support this mode of operation but custom implementations can do so. For the
  OSX/iOS clients to work, `list` must be enabled and accessible.
* `help` which unsurprisingly returns a help message about how the RPC works from the user's perspective. This task
  can be disabled or you can simply choose to not grant access to it, if your users do not require the help text. The
  task shows the contents of `CLIENT.md` from this extension as a response in the client.
* `demo` which is provided both as a way to demonstrate the basics of tasks and to serve as a quick reference for making
  common task types.
* `command` tasks for the only relevant command controller shipped with TYPO3: extension install and uninstall.

In addition to this a generic `command` task is provided can can be used to make individual command controllers show
up as tasks, allowing you to control exactly which ones can be used - for the cases when global access to all commands
is not desired. This generic task is enabled per command controller as follows:

```php
\NamelessCoder\Rpc\Implementation\Task\CommandTask::registerForCommand(
	\TYPO3\CMS\Extensionmanager\Command\ExtensionCommandController::class,
	'install'
)->setFieldTypeForArgument(
	\NamelessCoder\Rpc\Implementation\Field\AvailableExtensionsField::class,
	'extensionKey'
);
\NamelessCoder\Rpc\Implementation\Task\CommandTask::registerForCommand(
	\TYPO3\CMS\Extensionmanager\Command\ExtensionCommandController::class,
	'uninstall'
)->setFieldTypeForArgument(
	\NamelessCoder\Rpc\Implementation\Field\InstalledExtensionsField::class,
	'extensionKey'
);
```

This example shows how the extension install and uninstall commands are registered. Both use a custom field type for
the `extensionKey` argument - the fields deliver uninstalled or installed extension keys as a popup menu field. The
generic task's base code takes care of detecting arguments and calling the action on the controller. Use the same method
for your own command controllers, one for each action you wish to expose as RPC.

In TYPO3 it will often make a lot of sense to create your RPC tasks as command controllers and expose them using the
command controller task. This makes the tasks possible to run using the scheduler, from command line *and* using the
RPC client. However, for more complex tasks that for example require a lot of input arguments filled in multiple steps
or require a custom report when finished, you have to look into creating your own RPC task classes - see the `demo`
task's class for a quick reference.

See also "known limitations" below: command controllers are intended for a BE context but when executed this way will
actually have an FE context. Not all command controllers will support this - and some may need you to duplicate TS from
`module.tx_yourext.*` into `plugin.tx_yourext.*` due to the different context.

Feature completeness
--------------------

The following is a list of features, some of which are already implemented and some of which are planned for future
versions of the RPC extension and complementary OSX/iOS client applications:

**General features**

- [x] Base for creating custom Tasks
- [x] Client OSX application (El Capitan and up)
- [ ] Client iOS application
- [x] Client TYPO3 backend module
- [x] Success/error reporting for single fields, arguments as whole or Task success/error after execution
- [x] Multiple steps support for filling arguments one or more at a time
- [ ] Repeating of previous command with collected arguments
- [ ] Favorite commands+arguments storage for each connection
- [x] Conditional argument value support (by way of multiple steps where fields' values depend on previous fields)
- [x] CommandController integration to execute arbitrary TYPO3 CLI commands (which have been registered for RPC)
- [ ] Support for executing TYPO3RPC from command line to call tasks on a designated remote

**Component types**

- [x] Standard `<input type="text" />` equivalent
- [x] Combined `<input type="text" />` free typing and `<select>` with preset values equivalents
- [ ] DatePicker `<input type="text" />` equivalent with popup date selection
- [ ] Number picker `<input type="text" />` equivalent with value increment/decrement controls
- [x] Fulltext `<textarea>` equivalent
  - [x] Rich Text Format support OSX/iOS
  - [ ] RTF support in HTML client
- [x] Standard `<input type="checkbox" />` equivalent
- [ ] Standard `<input type="radio" />` equivalent
- [x] Standard `<select>` equivalent
  - [x] Single option selection support
  - [ ] Multiple option selection support
- [ ] Parent/child tree selection (custom HTML component equivalent)
- [ ] File selection field

**Payload features/support**

- [x] Scalar value output as string
- [x] Recursive array output support (table)
- [ ] URL recognition and link display
- [ ] Binary payload values (files, blobs) presented as downloads ("save as" dialog)

**Request/Response features**

- [x] Basic arguments support (single dimension array)
- [ ] Support for file transfers from client

Known limitations
-----------------

Nothing is perfect and that includes this package. I'm a beginner with Swift (these are in fact my first OSX *and* iOS
applications without things like PhoneGap).

The limitations are:

* RPC clients call the remote by hostname with all that this implies regarding how TYPO3 resolves the page ID (and thus
  the TS configuration and more). This is a potential pitfall on multisite setups combined with custom tasks that use
  TS or other page-specific configuration. A potential workaround can be to create multiple connections to the same
  remote on different hostnames using the same token since tokens are global.
* The OSX and iOS clients only work with HTTPS because of security settings on those platforms. IMHO this is only a
  good thing, but it would be neat to provide it as an option for people who are incapable of using HTTPS and handle
  security in other ways.
* The OSX and iOS clients only work on most recent versions of the platforms. This is a conscious decision - to avoid
  having to deal with legacy issues.
* The selection of components is currently a bit limited. For example there are no components which allow entering more
  than one value (unless of course you construct your task to accept CSV values). Although I've learned a lot about how
  to implement dynamic form fields in Swift I still need a bit more experience before I'm able to do it confidently.
* TYPO3 CommandController integration may not function with every conceivable CommandController. Most importantly there
  are differences in the contexts, namely that *TYPO3 CLI mode flag will not be set* and that *TYPO3 context will be
  frontend* (not backend like when running the CommandController on CLI).

Usage without TYPO3
-------------------

The package manifest does not explicitly require TYPO3 as a dependency. You can in fact use the package without TYPO3
by installing it as a composer dependency and manually constructing an entry point (inside another framework if needed).

To achieve this you will need:

* An implementation of `NamelessCoder\Rpc\Manager\TaskManagerInterface` registered with
  `NamelessCoder\Rpc\Manager\TaskManager::setInstance($instance);` before request is dispatched.
* An implementation of `NamelessCoder\Rpc\Manager\ClientManagerInterface` registered with
  `NamelessCoder\Rpc\Manager\ClientManager::setInstance($instance);` before request is dispatched.
* The entry point which makes an instance of `NamelessCoder\Rpc\RequestDispatcher` to call `handleIncomingRequest`

When used inside TYPO3 the package registers TYPO3-compatible implementations. When used outside you must manually
register implementations that are compatible with whichever storage/framework you want to use the RPC with.

The included `list`, `help` and `demo` tasks can be used anywhere - the `commands` and `command` implementations can
only be used with TYPO3 (but it is possible to create similar tasks for other frameworks' commands).
