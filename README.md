# Stop-Backse.at

Source code for stop-backse.at website

## Setting up dev env

Use [ddev](https://www.ddev.com/)

```bash
git clone git@github.com:emilweth/stop-backse.at.git
cd stop-backse.at/
ddev start
ddev exec composer install
ddev exec yarn install
yarn dev-server
```

Then go to http://stop-backse.at.ddev.site/

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.