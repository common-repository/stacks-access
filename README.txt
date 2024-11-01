=== Web3devs Stacks Access Plugin ===
Contributors: web3devs
Donate link: https://stacksaccess.com
Tags: stacks, blockchain, access
Requires at least: 5.9.2
Requires PHP: 7.0+
Tested up to: 6.0
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Limit access to Posts and Pages based on user's [Stacks](https://www.stacks.co/) wallet content (ex. STX, Fungible Tokens or NFTs).

== Description ==

Stacks Access Plugin allows users to define which Posts or Pages have limited access based on user's [Hiro](https://www.hiro.so/wallet) wallet contents (STX, NFTs, Fungible Tokens).

Ex. let's say you own Crypto Ducks Club NFTs and want your blog content to be available only to other Crypto Ducks Club owners - with this plugin, you can do that.

== Installation ==

1. Upload `web3devs-stacks-access.zip` to the `/wp-content/plugins/` directory
2. Unzip the file
3. Remove the zip file, leave the directory
4. Activate the plugin through the 'Plugins' menu in WordPress
5. Go to "Stacks Access > Settings" and configure token contract addresses (or stx/STX for STX)
6. Go to Page/Post and use Stacks Access Widget (at the bottom) to configure access restrictions

== Frequently Asked Questions ==

= What wallets do you support? =

[Hiro Wallet](https://www.hiro.so/wallet) only.

= Do you plan to support any other wallets? =

Short answer: no.

Long answer: [Xverse](https://www.xverse.app/) - maybe. [Wallet Connect](https://walletconnect.com/) compatible wallets - maybe.

If a wallet is compatible with [Stacks.js](https://www.hiro.so/stacks-js), then it should work.

Hiro Wallet is the de facto standard for Stacks &#129335;

= What PHP extensions are required for this plugin to work? =

None.

The ones that we would **probably** need for this to be 100% PHP based are:

- [secp256k1](https://github.com/Bit-Wasp/secp256k1-php) - but it's experimental, requires manual compilation and it's development is staled
- [c32check](https://github.com/stacks-network/c32check) - it's Stacks' variant, so has to be reimplemented in PHP
- [stacks.js](https://www.hiro.so/stacks-js) - but in PHP :)

Since none of the above is available at the moment, we've moved the required dependencies to a third party service that simply takes the signature and calculates Public Address used to sign it.

= What is the transaction cost (gas fee) of determining the access? =

0 (zero).

There's no cost, because there's no transaction involved.

Signing a secret phrase is the wallet's built in functionality. It happens "locally" and is not published to the blockchain, therefore does not end up as a transaction.

= Why do you need access to my wallet? =

We don't.

We don't need it and we don't access it.

We need your wallet's **public address**. To prove **you OWN the address**, we need you to use the wallet to sign a secret phrase for us (your wallet uses your **private key** to do it - **the key never leaves your computer!**).

Your wallet contents is publicly available information. If you know a wallet's address, ex. **ST24YYAWQ4DK4RKCKK1RP4PX0X5SCSXTWQXFGVCVY** you can see it's contents in blockchain explorer or via Stacks API - that's how we determine if you own configured tokens.

See:
- [API view](https://stacks-node-api.testnet.stacks.co/extended/v1/address/ST24YYAWQ4DK4RKCKK1RP4PX0X5SCSXTWQXFGVCVY/balances "ST24 address through Stacks API")
- [Stacks Explorer](https://explorer.stacks.co/address/ST24YYAWQ4DK4RKCKK1RP4PX0X5SCSXTWQXFGVCVY?chain=testnet "ST24 address through Stacks Explorer")

If **we (or ANYONE ELSE)** ask you for your **private key** or **seed/mnemonic phrase** - **THAT'S SOMETHING you should be worried about**

= Why does it say "testnet" when I'm on mainnet? =

It's due to a bug in Hiro Wallet, see [here](https://github.com/hirosystems/stacks-wallet-web/issues/2463).

Basically, when connecting Hiro Wallet for signing, the account selection screen **sometimes** makes no effect and your current account is selected.

Pay attention to account public address shown at the top of the screens to make sure you're signing with desired account.

Otherwise - switch it in the wallet, before connecting (refresh the page to "disconnect" your wallet if needed, sometimes you may need to close your browser (clear session cookies))

= Are there any other Stacks/Hiro bugs I should be concerned about? =

Yes.

Message signing (which we use to derive your wallet's public address) is a relatively new feature in Hiro Wallet and Stacks itself and it's been published prematurely :)

There are (were) problems with:
- message signing in a way that prevented users from deriving public address from signatures (see [here](https://github.com/hirosystems/stacks-wallet-web/issues/2435), [here](https://github.com/hirosystems/stacks.js/pull/1260) and [here](https://github.com/hirosystems/stacks-wallet-web/issues/2419))
- incompatible secp256k1 signature orders (vrs vs rsv) (see [here](https://github.com/hirosystems/stacks.js/pull/1263))

The good thing is: it's a known problem
The bad thing is: when it's fixed, chances are our plugin will stop working until we make it compatible with the new versions of Hiro Wallet and Stacks.js

== Screenshots ==

1. Plugin on plugins list
2. Token configuration screen (Settings)
3. Connect wallet triggered on a page with restricted access
4. Selecting account
5. Signing message

== Changelog ==

= 1.0.3 =
* Changed logo again

= 1.0.2 =
* Changed logo

= 1.0.1 =
* Fix: Issue with "testnet" phrase showing up when "mainnet" account selected

= 1.0.0 =
* Mainnet / Testnet compatible Stacks Access plugin

== Upgrade Notice ==

= 1.0.0 =
* Mainnet / Testnet compatible Stacks Access plugin