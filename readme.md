# Minepic API Website

![Build](https://img.shields.io/github/workflow/status/mattiabasone/minepic/Testing%20App%20(Lumen%20with%20MySQL)/master)
[![Coverage Status](https://coveralls.io/repos/github/mattiabasone/minepic/badge.svg?branch=master)](https://coveralls.io/github/mattiabasone/minepic?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mattiabasone/minepic/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mattiabasone/minepic/?branch=master)

Minepic is a simple API for displaying Minecraft avatars and skins, it's based on [Lumen](https://lumen.laravel.com/).

### Endpoints

##### Avatar

Base
`https://minepic.org/avatar/{uuid|username}`

With size
`https://minepic.org/avatar/{uuid|username}/{size}`

![Avatar](https://minepic.org/avatar/_Cyb3r/64)

##### Head

Base
`https://minepic.org/head/{uuid|username}`

With size
`https://minepic.org/head/{uuid|username}/{size}`

![Head](https://minepic.org/head/_Cyb3r/64)

#### Skin

Front `https://minepic.org/skin/{uuid|username}`

Front with size `https://minepic.org/skin/{uuid|username}/{size}`

![Head](https://minepic.org/skin/_Cyb3r/64)

Back `https://minepic.org/skin-back/{uuid|username}`

Back with size `https://minepic.org/skin/{uuid|username}/{size}`

![Head](https://minepic.org/skin-back/_Cyb3r/64)

#### Download

`https://minepic.org/download/{uuid|username}`

#### Utility

Update user information `https://minepic.org/update/{uuid}`