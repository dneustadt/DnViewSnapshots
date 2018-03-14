# View Snapshots

View Snapshots is a plugin for shopware that allows you to record/log requests
continiously. It will record snapshots of variables passed to the view and
will persist them in serialized form to the database. Also persisted is the
template path, session ID and steps taken since the start of recording.

## Goal

By having a snapshot of the template, including it's data, you can review most
requests with the more or less exact same outcome as the original request.
Therefore you can customize your templates, css, JavaScript and check your
changes against a previously recorded situation, e.g. a variety of different
articles within the basket or the order confirmation.

## Features

* Start/stop recording sessions at any time
* Backend component to view recorded sessions
* View recorded snapshots
* Step forward/backward between snapshots within a recorded session

## Usage

After installing the plugin through the plugin manager, you can find
the accompanying backend app under the Configuration menu.

The backend app will list all recorded sessions.

To start a recording session, request a frontend page and use the
JavaScript console to issue commands:

**Start recording**

```
> snapshots.record()  
▶️️ Recording of session starting next request. Session ID: 0sorspi2md1j2vpt9tn9po3om1
```

**Stop recording**

```
> snapshots.stop()  
✋️️ Stopped recording current session.
```

#### When viewing a recorded session

**View next step in recorded session**

```
> snapshots.next()
```

**View previous step in recorded session**

```
> snapshots.prev()
```