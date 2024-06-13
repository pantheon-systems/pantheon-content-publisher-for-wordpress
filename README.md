<h3 align="center">Pantheon Content Cloud for WordPress</h3>

<h4>Local Development</h4>
1. Run `composer i`
2. Run `npm i`
3. Run `npm run dev`
4. To authenticate with Google, run `composer run auth <local-wordpress.tld>` where `<local-wordpress.tld>` is the domain of your local WordPress site. 
   1. **This will expose a server on `localhost:3030` as PCC has that local domain set as an authenticated redirect URI.**
   2. If you are using `valet` for local development, you can run `composer run auth` with no argument, as the domain will be pulled from `valet`. 
