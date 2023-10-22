# How to run the project

###### Set .env
```shell
cp .env.example .env
```

###### You should check that params **HOST_UID** and **HOST_GID** equals the next values
```shell
id -u && id -g
```

###### Build and run docker
```shell
make install
```

###### How to prepare test env and run tests

```shell
make test
```