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
Include the Facade KoiosApi in the controller where you like to query the Koios API.
```php
use CardanoBlockhouse\CardanoKoiosApi\Facades\KoiosApi;
```

Choose the network to query
```php
KoiosApi::setNetwork('mainnet'); // Not needed to set as this is the default option
KoiosApi::setNetwork('preview');
KoiosApi::setNetwork('preprod');
```

### Horizontal filtering
Example
```php
$horizontal_filter = ['epoch=eq.250', 'epoch_slot=lt.180'];
```


### Network
```php
KoiosApi::network_fetchTip()
KoiosApi::network_fetchGenesis()
KoiosApi::network_fetchTotals(string $epoch_no = null, array $horizontal_filter = null)
KoiosApi::network_fetchParamUpdates(array $horizontal_filter = null)
```
### Epoch
```php
KoiosApi::epoch_fetchEpochInfo(string $epoch_no = null, string $include_next_epoch = null, array $horizontal_filter = null)
KoiosApi::epoch_fetchEpochParams(string $epoch_no = null, array $horizontal_filter = null)
KoiosApi::epoch_fetchEpochBlockProtocols(string $epoch_no = null, array $horizontal_filter = null)
```
### Block
```php
KoiosApi::block_fetchBlocks(array $horizontal_filter = null)
KoiosApi::block_fetchBlockInformation(array $block_hashes)
KoiosApi::block_fetchBlockTransactions(array $block_hashes)
```
### Transactions
```php
KoiosApi::transaction_fetchTransactionInfos(array $tx_hashes)
KoiosApi::transaction_fetchTransactionMetadata(array $tx_hashes)
KoiosApi::transaction_fetchTransactionMetadataLabels(array $horizontal_filter = null)
KoiosApi::transaction_fetchTransactionStatus(array $tx_hashes)
```
### Address
```php
KoiosApi::address_fetchAddressInfo(array $addresses)
KoiosApi::address_fetchAddressTxs(array $addresses, int $after_block_height = null)
KoiosApi::address_fetchCredentialUtxos(array $payment_credentials)
KoiosApi::address_fetchAddressAssets(array $addresses)
KoiosApi::address_fetchCredentialTxs(array $payment_credentials, int $after_blockheight = null)
```
### Asset
```php
KoiosApi::asset_fetchAssetList(array $horizontal_filter = null)
KoiosApi::asset_fetchAssetTokenRegistry(array $horizontal_filter = null)
KoiosApi::asset_fetchAssetAddresses(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null)
KoiosApi::asset_fetchAssetNftAddress(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null)
KoiosApi::asset_fetchAssetInfo(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null)
KoiosApi::asset_fetchAssetInfoBulk(array $asset_list)
KoiosApi::asset_fetchAssetHistory(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null)
KoiosApi::asset_fetchPolicyAssetAddresses()
KoiosApi::asset_fetchPolicyAssetInfo(string $asset_policy, array $horizontal_filter = null)
KoiosApi::asset_fetchPolicyAssetList(string $asset_policy, array $horizontal_filter = null)
KoiosApi::asset_fetchAssetSummary(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null)
KoiosApi::asset_fetchAssetTxs(string $asset_policy, string  $asset_name = null, int $after_block_height = null, string $history = null, array $horizontal_filter = null)
```
### Pool
```php
KoiosApi::pool_fetchPoolList(array $horizontal_filter = null)
KoiosApi::pool_fetchPoolInfo(array $pool_bech32_ids)
KoiosApi::pool_fetchPoolStakeSnapshot(string $pool_bech32, array $horizontal_filter = null)
KoiosApi::pool_fetchPoolDelegators(string $pool_bech32, array $horizontal_filter = null)
KoiosApi::pool_fetchDelegatorsHistory(string $pool_bech32, string $epoch_no = null, array $horizontal_filter = null)
KoiosApi::pool_fetchPoolBlocks(string $pool_bech32, string $epoch_no = null, array $horizontal_filter = null)
KoiosApi::pool_fetchPoolHistory(string $pool_bech32, string $epoch_no = null, array $horizontal_filter = null)
KoiosApi::pool_fetchPoolUpdates(string $pool_bech32, array $horizontal_filter = null)
KoiosApi::pool_fetchPoolRelays(array $horizontal_filter = null)
KoiosApi::pool_fetchPoolMetadata(array $pool_bech32_ids)
```
### Script
```php
KoiosApi::script_fetchNativeScriptList(array $horizontal_filter = null)
KoiosApi::script_fetchPlutusScriptList(array $horizontal_filter = null)
KoiosApi::script_fetchScriptRedeemers(string $script_hash, array $horizontal_filter = null)
KoiosApi::script_fetchDatumInfo(array $datum_hashes)
```
### Stake Account
```php
KoiosApi::account_fetchAccountList(array $horizontal_filter = null)
KoiosApi::account_fetchAccountInfos(array $stake_addresses)
KoiosApi::account_fetchAccountUtxos(string $stake_address, array $horizontal_filter = null)
KoiosApi::account_fetchAccountInfoCached(array $stake_addresses)
KoiosApi::account_fetchAccountRewards(array $stake_addresses)
KoiosApi::account_fetchAccountUpdates(array $stake_addresses)
KoiosApi::account_fetchAccountAddresses(array $stake_addresses)
KoiosApi::account_fetchAccountAssets(array $stake_addresses)
KoiosApi::account_fetchAccountHistory(array $stake_addresses)
```
## Bugs and Suggestions

## Copyright and License

[MIT](https://choosealicense.com/licenses/mit/)
