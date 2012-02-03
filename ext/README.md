# Protobuf-PHP C extension

Decoding the Protocol Buffers binary format using only _user functions_ is slow
due to the nature of the PHP language and interpreter. This C language extension
to the PHP interpreter exposes a set of functions that allow the library to delegate
the decoding of binary messages to a C runtime provided by the [lwpb](http://code.google.com/p/lwpb/)
library.

## Performance

The observed performance when using the extension is that it is between 2 and 3 times
faster than using `json_decode` on the same data. Obviously this doesn't take into account
the time needed to map the decoded array structures to the generated PHP classes of the
messages.

TBD


## Installing

The build setup is only compatible with *nix like systems (Bsd, Linux, OSX). There is
no automated way neither instructions to build the extension for Windows systems, sorry,
please feel free to provide them if you have the knowledge.

The following steps should get the extension ready to be used in your PHP installation:

    phpize
    ./configure --enable-pbext
    make
    make test
    make install

If everything works as expected and no errors are shown with those commands, then you
should have the compiled shared library containing the extension in the correct location.
Now it's only a matter of enabling it in your `php.ini` file. Just add the following line
to your configuration:

    extension=pbext.so


## API

While the Protobuf-PHP library already provides a _frontend_ to the functions provided 
by the extension, it might be desirable for some extreme use cases, to bypass it completely 
and use directly the extension.

> To allow the implementation of a _lazy decoding_ scheme, just define nested message fields
as having a binary type. The decoder will return the original encoded string without further
analyzing it.


### resource pbext_desc_message( $name = NULL )

Create a new message descriptor to whom attach field descriptors with `pbext_desc_field`.
The return value of this function is a `resource`, as such it's opaque in the PHP user land, 
it's need however to bind field descriptors to messages and to define nested messages.

Optionally you can assign a descriptive name to the message descriptor.

> Take into account that the created resources will not be freed until the script ends, so
  remember to store the returned value somewhere instead of creating a descriptor every time
  you want to decode a given message.

### void pbext_desc_field( $message, $number, $label, $type, $name = NULL, $flags = 0, $nested = NULL )

TBD

### mixed pbext_decode( $message, $data )

TBD


## License

The extension code is assumed to be part of the Protobuf-PHP package and as such is
distributed under the Mit license. The Lightweight Protocol Buffers project however 
is distributed according to the Apache License 2.0. This specifically means that
for the files residing in the `lwpb` directory the Apache License 2.0 applies, while
the remaining files fall under the Mit license terms.
