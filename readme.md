# Minepic API Website

Minepic is a simple API for displaying Minecraft avatars and skins, it's based on [Lumen](https://lumen.laravel.com/).

### Endpoints

##### Avatar

Base
`https://minepic.org/avatar/{uuid|username}`

With size
`https://minepic.org/avatar/{size}/{uuid|username}`

![Avatar](https://minepic.org/avatar/64/_Cyb3r)

##### Head

Base
`https://minepic.org/head/{uuid|username}`

With size
`https://minepic.org/head/{size}/{uuid|username}`

![Head](https://minepic.org/head/64/_Cyb3r)

#### Skin

Front `https://minepic.org/skin/{uuid|username}`

Front with size `https://minepic.org/skin/{size}/{uuid|username}`

![Head](https://minepic.org/skin/64/_Cyb3r)

Back `https://minepic.org/skin-back/{uuid|username}`

Back with size `https://minepic.org/skin/{size}/{uuid|username}`

![Head](https://minepic.org/skin-back/64/_Cyb3r)

#### Download

`https://minepic.org/download/{uuid|username}`

#### Utility

Update user information `https://minepic.org/update/{uuid|username}`