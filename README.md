# cardano-koios-api
cardano-koios-api is a Laravel package to use the cardano Koios API in your laravel project and to access the blockchain data of cardano.

- [cardano-koios-api](#iamx-wallet-pro)
    - [Installation](#Installation)
    - [Configuration](#Configuration)
    - [Usage](#Usage)
    - [Bugs, Suggestions, Contributions and Support](#bugs-and-suggestions)
    - [Copyright and License](#copyright-and-license)

## Installation

Install the current version of the `cardano-blockhouse/cardano-koios-api` package via composer:
```sh
    composer require cardano-blockhouse/cardano-koios-api:dev-main
```

## Configuration


## Usage
### Network
```php
KoiosApi::network_fetchTip()
KoiosApi::network_fetchGenesis()
KoiosApi::network_fetchTotals()
KoiosApi::network_fetchParamUpdates()
```
### Epoch
```php
KoiosApi::epoch_fetchEpochInfo()
KoiosApi::epoch_fetchEpochParams()
KoiosApi::epoch_fetchEpochBlockProtocols()
```
### Block
```php
KoiosApi::block_fetchBlocks()
KoiosApi::block_fetchBlockInformation()
KoiosApi::block_fetchBlockTransactions()
```
### Transactions
```php
KoiosApi::transaction_fetchTransactionInfos()
KoiosApi::transaction_fetchTransactionMetadata()
KoiosApi::transaction_fetchTransactionMetadataLabels()
KoiosApi::transaction_submitTransaction()
KoiosApi::transaction_fetchTransactionStatus()
```
### Address
```php
KoiosApi::address_fetchAddressInfo()
KoiosApi::address_fetchAddressTxs()
KoiosApi::address_fetchCredentialUtxos()
KoiosApi::address_fetchAddressAssets()
KoiosApi::address_fetchCredentialTxs()
```
### Asset
```php
KoiosApi::asset_fetchAssetList()
KoiosApi::asset_fetchAssetTokenRegistry()
KoiosApi::asset_fetchAssetAddresses()
KoiosApi::asset_fetchAssetNftAddress()
KoiosApi::asset_fetchAssetInfo()
KoiosApi::asset_fetchAssetInfoBulk()
KoiosApi::asset_fetchAssetHistory()
KoiosApi::asset_fetchPolicyAssetAddresses()
KoiosApi::asset_fetchPolicyAssetInfo()
KoiosApi::asset_fetchPolicyAssetList()
KoiosApi::asset_fetchAssetSummary()
KoiosApi::asset_fetchAssetTxs()
```
### Pool
```php
KoiosApi::pool_fetchPoolList()
KoiosApi::pool_fetchPoolInfo()
KoiosApi::pool_fetchPoolStakeSnapshot()
KoiosApi::pool_fetchPoolDelegators()
KoiosApi::pool_fetchDelegatorsHistory()
KoiosApi::pool_fetchPoolBlocks()
KoiosApi::pool_fetchPoolHistory()
KoiosApi::pool_fetchPoolUpdates()
KoiosApi::pool_fetchPoolRelays()
KoiosApi::pool_fetchPoolMetadata()
```
### Script
```php
KoiosApi::script_fetchNativeScriptList()
KoiosApi::script_fetchPlutusScriptList()
KoiosApi::script_fetchScriptRedeemers()
KoiosApi::script_fetchDatumInfo()
```
### Stake Account
```php
KoiosApi::account_fetchAccountList()
KoiosApi::account_fetchAccountInfos()
KoiosApi::account_fetchAccountUtxos()
KoiosApi::account_fetchAccountInfoCached()
KoiosApi::account_fetchAccountRewards()
KoiosApi::account_fetchAccountUpdates()
KoiosApi::account_fetchAccountAddresses()
KoiosApi::account_fetchAccountAssets()
KoiosApi::account_fetchAccountHistory()
```
## Bugs and Suggestions

## Copyright and License

[MIT](https://choosealicense.com/licenses/mit/)
