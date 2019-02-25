const wpPot = require('wp-pot');
const plugin_info = require('./package');
const plugin_name = plugin_info.name;

var pathToPot = 'languages/' + plugin_name + '.pot';

console.log('Making POT file...');

wpPot({
    destFile: pathToPot,
    package: plugin_name,
    src: ['includes/**/*.php']
});

console.log('POT file created.');

