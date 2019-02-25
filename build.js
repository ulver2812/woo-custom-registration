const fse = require('fs-extra');
const zip = require('zip-dir');
const plugin_info = require('./package.json');
const plugin_name = plugin_info.name;

fse.emptyDirSync('dist/');
fse.emptyDirSync('dist/' + plugin_name);
fse.copySync('includes', 'dist/' + plugin_name + '/includes');
fse.copySync('languages', 'dist/' + plugin_name + '/languages');
fse.copySync('assets', 'dist/' + plugin_name + '/assets');
fse.copySync('index.php', 'dist/' + plugin_name + '/index.php');
fse.copySync('LICENSE.txt', 'dist/' + plugin_name + '/LICENSE.txt');
fse.copySync('README.md', 'dist/' + plugin_name + '/README.txt');
fse.copySync('README.md', 'dist/' + plugin_name + '/README.md');
fse.copySync('' + plugin_name + '.php', 'dist/' + plugin_name + '/' + plugin_name + '.php');
fse.copySync('uninstall.php', 'dist/' + plugin_name + '/uninstall.php');

// SET PLUGIN VERSION
fse.writeFileSync('dist/' + plugin_name + '/' + plugin_name + '.php', fse.readFileSync('dist/' + plugin_name + '/' + plugin_name + '.php', 'utf8').replace(/{{plugin-version}}/g, plugin_info.version), 'utf8');
fse.writeFileSync('dist/' + plugin_name + '/README.txt', fse.readFileSync('dist/' + plugin_name + '/README.txt', 'utf8').replace(/{{plugin-version}}/g, plugin_info.version), 'utf8');
fse.writeFileSync('dist/' + plugin_name + '/README.md', fse.readFileSync('dist/' + plugin_name + '/README.md', 'utf8').replace(/{{plugin-version}}/g, plugin_info.version), 'utf8');

zip('dist/', {saveTo: 'dist/' + plugin_name + '_' + plugin_info.version + '.zip'}, function (err, buffer) {
    fse.removeSync('dist/' + plugin_name + '');
});


