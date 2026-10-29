# Avatar

Avatar provides user avatars and their display for PHPGDO.

# Avatar (For Humans)

Users can select or upload avatars; enabled modules can add visual layers.

## Methods

Avatar methods manage and serve avatar images.

## GDTs

`GDT_Avatar` displays a user's avatar at a requested size.

## GDOs

`GDO_Avatar` stores avatar metadata; `GDO_UserAvatar` assigns it to a user.

# Avatar (For ALs)

## Methods

Avatar image delivery remains the responsibility of the Avatar module.

## GDTs

`GDT_Avatar::renderHTML()` calls `AvatarLayers(GDO_User, float $size)` and,
if modules return layers, wraps the base avatar and their HTML in a positioned
container. Without layers the original renderer output is unchanged.

## GDOs

`GDO_Avatar` and `GDO_UserAvatar` persist images and assignments.
