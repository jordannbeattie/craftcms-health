# Image Optimization
Render images in your Craft templates with a single include. Pass in classes, fallbacks, transforms and more and have optimized images on your site compliant with Google Page Speed Insights. 

## Installation
Include the package in your project: 
```
composer require jordanbeattie/craftcms-images
```
and install the plugin within the CMS. 

## Usage
Instead of writing out `<img>` tags in your project, pass variables to the render function which will optimize the image for you. 

| Variable | Description | 
| --- | --- |
| Field | The CraftCMS field where users can upload a single image.|
| Transform | The handle of an image transform to use. _*See transforms._ |
| Fallback | A static URL/path to an image that can be used if there is an issue rendering the image or if the field is empty. |
| Class | Any classes that should be added to the tag |
| Style | Any items to be included in the `style` attributes |
| Alt | Text for the alt attribute. If none is supplied and the field variable is passed, the alt text from the image model will be used. |
| Attributes | Any other attributes to be added to the tag. This should be an array. _* See attributes._ |

### Transforms
Including transforms is the best way to serve images. You can pass the transform handle through to the include. 
The plugin will look for the transform with the handle provided and also a mobile version which should have the same handle appended with "Mobile". 
e.g. _myTransformHandle_ and _myTransformHandleMobile_.

### Attributes
Often, when using third-party packages such as MatchHeight, you will need to add custom attributes. These can be passed in as an array. 
```
attributes: {
    'data-mh': 'my-image'
}
```

## Examples
#### Full parameters
```
{{ craft.images.render(block.image, {
    transform: 'blockTransform', 
    fallback: 'https://example.com/fallback-image.png', 
    class: 'w-full h-full hidden md:block', 
    style: 'display:none',
    attributes: {
        'data-mh': 'my-block-image'
    }
}) }}
```

#### Image without transform
```
{{ craft.images.render(block.image) }}
```

#### Static image
```
{{ craft.images.render({
    fallback: 'https://example.com/fallback-image.png'
) }}
```

#### Replacing an existing img tag
```
<img alt="" class="block max-w-full p-0 m-0 rounded-lg pointer-events-none select-none" src="{{ tab.image.one().url }}" />
```
should be replaced with
```
{{ craft.images.render(tab.image, {
    class: "block max-w-full p-0 m-0 rounded-lg pointer-events-none select-none"
) }}
```
or with a transform
```
{{ craft.images.render( tab.image, {
    transform: 'tabBlockImage',
    class: "block max-w-full p-0 m-0 rounded-lg pointer-events-none select-none"
} }}
```

## Contact
Jordan Beattie <br>
jordan@jordanbeattie.com <br>
www.jordanbeattie.com
