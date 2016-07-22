#!/bin/sh

# regenerate the protos
# protoc-gen-php -i . -o . google/pubsub/v1/pubsub.proto

# run the client
php -d extension=grpc.so pubsub-sample.php

