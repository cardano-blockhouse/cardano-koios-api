
# cardano-koios-api
cardano-koios-api is a Laravel package to use the cardano Koios API in your laravel project and to access the blockchain data of cardano.

- [cardano-koios-api](#iamx-wallet-pro)
    - [Installation](#installation)
    - [Configuration](#configuration)
    - [Usage](#usage)
        - [Horizontal filtering](#horizontal-filtering)
        - [Pagination](#pagination)
        - [Example](#example)
        - [Endpoints](#endpoints)
            - [Network](#network)
            - [Epoch](#epoch)
            - [Block](#block)
            - [Transactions](#transactions)
            - [Address](#address)
            - [Asset](#asset)
            - [Pool](#pool)
            - [Script](#script)
            - [Stake Account](#stake-account)
    - [Bugs, Suggestions](#bugs-and-suggestions)
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
https://api.koios.rest/#overview--horizontal-filtering

Example
```php
$horizontal_filter = ['epoch=eq.250', 'epoch_slot=lt.180'];
```

### Pagination
Pagination is automatically done in the backend in chunks of 500 rows per API call.

### Example
```php
<?php

namespace App\Http\Controllers;

use CardanoBlockhouse\CardanoKoiosApi\Facades\KoiosApi;

class TestController extends Controller
{
    public function test() {
        KoiosApi::setNetwork('mainnet');
        foreach (KoiosApi::block_fetchBlocks(['epoch_no=eq.429', 'block_time=gt.1691657145']) as $block) {
            echo 'Epoch no: '.$block->epoch_no.' - Abs slot: '.$block->abs_slot;
        }
    }
}
```
### Endpoints

#### Network
```php
/*
 * Get the tip info about the latest block seen by chain
 *
 * GET /tip
 *
 * @return NetworkTip
 */
KoiosApi::network_fetchTip();

/*
 * Get the Genesis parameters used to start specific era on chain
 *
 * GET /genesis
 *
 * @return NetworkGenesis
 */
KoiosApi::network_fetchGenesis();

/*
 * Get the circulating utxo, treasury, rewards, supply and reserves in lovelace for specified epoch, all epochs if
 * empty
 *
 * GET /totals
 *
 * @param string epoch_no (optional)
 * @param array horizontal_filter (optional)
 * @return Collection<NetworkTotals>
 */
KoiosApi::network_fetchTotals(string $epoch_no = null, array $horizontal_filter = null);

/*
 * Get all parameter update proposals submitted to the chain starting Shelley era
 *
 * GET /param_updates
 *
 * @param array horizontal_filter (optional)
 * @return Collection<NetworkParamUpdates>
 */
KoiosApi::network_fetchParamUpdates(array $horizontal_filter = null);
```
#### Epoch
```php
/*
 * Get the epoch information, all epochs if no epoch specified
 *
 * GET /epoch_info
 *
 * @param epoch_no (optional)
 * @param include_next_epoch (optional)
 * @param array horizontal_filter (optinal)
 * @return Collection<EpochInfo>
 */
KoiosApi::epoch_fetchEpochInfo(string $epoch_no = null, string $include_next_epoch = null, array $horizontal_filter = null);

/*
 * Get the protocol parameters for specific epoch, returns information about all epochs if no epoch specified
 *
 * GET /epoch_params
 *
 * @param epoch_no (optional)
 * @param array horizontal_filter (optional)
 * @return Collection<EpochParams>
 */
KoiosApi::epoch_fetchEpochParams(string $epoch_no = null, array $horizontal_filter = null);

/*
 * Get the information about block protocol distribution in epoch
 *
 * GET /epoch_block_protocols
 *
 * @param epoch_no (optional)
 * @param array horizontal_filter (optional)
 * @return Collection<EpochBlockProtocols>
 */
KoiosApi::epoch_fetchEpochBlockProtocols(string $epoch_no = null, array $horizontal_filter = null);
```
#### Block
```php
/*
 * Get summarised details about all blocks (paginated - latest first)
 *
 * GET /blocks
 *
 * @param array horizontal_filter (optinal)
 * @return Collection<BlockList>
 */
KoiosApi::block_fetchBlocks(array $horizontal_filter = null);

/*
 * Get detailed information about a specific block
 *
 * POST /block_info
 *
 * @param array block_hashes
 * @return Collection<BlockInformation>
 */
KoiosApi::block_fetchBlockInformation(array $block_hashes);

/*
 * Get a list of all transactions included in provided blocks
 *
 * POST /block_txs
 *
 * @param array block_hashes
 * @return Collection<BlockTransactions>
 */
KoiosApi::block_fetchBlockTransactions(array $block_hashes)
```
#### Transactions
```php
/*
 * Get detailed information about transaction(s)
 *
 * POST /tx_info
 *
 * @param array tx_hashes
 * @return Collection<TransactionInfos>
 */
KoiosApi::transaction_fetchTransactionInfos(array $tx_hashes);

/*
 * Get metadata information (if any) for given transaction(s)
 *
 * POST /tx_metadata
 *
 * @param array tx_hashes
 * @return
 */
KoiosApi::transaction_fetchTransactionMetadata(array $tx_hashes);

/*
 * Get a list of all transaction metalabels
 *
 * GET /tx_metalabels
 *
 * @param array horizontal_filter (optional)
 * @return Collection<TransactionMetadataLabels>
 */
KoiosApi::transaction_fetchTransactionMetadataLabels(array $horizontal_filter = null);

/*
 * Get the number of block confirmations for a given transaction hash list
 *
 * POST /tx_status
 * 
 * @param array tx_hashes
 * @return Collection<TransactionStatus>
 */
KoiosApi::transaction_fetchTransactionStatus(array $tx_hashes);
```
#### Address
```php
/*
 * Get address info - balance, associated stake address (if any) and UTxO set for given addresses
 *
 * POST /address_info
 *
 * @param array addresses
 * @return Collection<AddressInformation>
 */
KoiosApi::address_fetchAddressInfo(array $addresses);

/*
 * Get the transaction hash list of input address array, optionally filtering after specified block height
 * (inclusive)
 *
 * POST /address_txs
 *
 * @param array addresses
 * @param int after_block_height (optional)
 * @return Collection<AddressTransactions>
 */
KoiosApi::address_fetchAddressTxs(array $addresses, int $after_block_height = null);

/*
 * Get a list of UTxO against input payment credential array including their balances
 *
 * POST /credential_utxos
 *
 * @param array payment_credentials
 * @return Collection<CredentialUtxos>
 */
KoiosApi::address_fetchCredentialUtxos(array $payment_credentials);

/*
 * Get the list of all the assets (policy, name and quantity) for given addresses
 *
 * POST /address_assets
 *
 * @param array addresses
 * @return Collection<AddressAssets>
 */
KoiosApi::address_fetchAddressAssets(array $addresses);

/*
 * Get the transaction hash list of input payment credential array, optionally filtering after specified block
 * height (inclusive)
 *
 * POST /credential_txs
 *
 * @param array payment_credentials
 * @param int after_blockheight (optional)
 * @return Collection<CredentialUtxos>
 */
KoiosApi::address_fetchCredentialTxs(array $payment_credentials, int $after_blockheight = null)
```
#### Asset
```php
/*
 * Get the list of all native assets (paginated)
 *
 * GET /asset_list
 *
 * @param array $horizontal_filter (optional)
 *
 * @return Collection<AssetList>
 */
KoiosApi::asset_fetchAssetList(array $horizontal_filter = null);

/*
 * Get a list of assets registered via token registry on github
 *
 * GET /asset_token_registry
 *
 * @param array horizontal_filter (optional)
 * @return Collection<AssetTokenRegistry>
 */
KoiosApi::asset_fetchAssetTokenRegistry(array $horizontal_filter = null);

/*
 * Get the list of all addresses holding a given asset
 *
 * Note - Due to cardano's UTxO design and usage from projects, asset to addresses map can be infinite. Thus, for a
 * small subset of active projects with millions of transactions, these might end up with timeouts (HTTP code 504)
 * on free layer. Such large-scale projects are free to subscribe to query layers to have a dedicated cache table
 * for themselves served via Koios.
 *
 * GET /asset_addresses
 *
 * @param string asset_policy
 * @param string asset_name (optional)
 * @param array horizontal_filter (optional)
 * @return Collection<AssetAddresses>
 */
KoiosApi::asset_fetchAssetAddresses(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null);

/*
 * Get the address where specified NFT currently reside on.
 *
 * GET /asset_nft_address
 *
 * @param string asset_policy
 * @param string asset_name (optional)
 * @param array horizontal_filter (optional)
 * @return Collection<AssetNFTAddress>
 */
KoiosApi::asset_fetchAssetNftAddress(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null);

/*
 * Get the information of an asset including first minting & token registry metadata
 *
 * GET /asset_info
 *
 * @param string asset_policy
 * @param string asset_name (optional)
 * @param array horizontal_filter (optinal)
 * @return Collection<AssetInfo>
 */
KoiosApi::asset_fetchAssetInfo(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null);

/*
 * Get the information of a list of assets including first minting & token registry metadata
 *
 * POST /asset_info
 *
 * @param array asset_list
 * @return Collection<AssetList>
 */
KoiosApi::asset_fetchAssetInfoBulk(array $asset_list);;

/*
 * Get the mint/burn history of an asset
 *
 * GET /asset_history
 *
 * @param string asset_policy
 * @param string asset_name (optional)
 * @param array horizontal_filter (optional)
 * @return Collection<AssetHistory>
 */
KoiosApi::asset_fetchAssetHistory(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null);

/*
 * Get the list of addresses with quantity for each asset on the given policy
 *
 * Note - Due to cardano's UTxO design and usage from projects, asset to addresses map can be infinite. Thus, for a
 * small subset of active projects with millions of transactions, these might end up with timeouts (HTTP code 504)
 * on free layer. Such large-scale projects are free to subscribe to query layers to have a dedicated cache table
 * for themselves served via Koios.
 *
 * GET /policy_asset_addresses
 *
 * @param string asset_policy
 * @param array horizontal_filter (optional)
 * @return Collection<PolicyAssetAddressList>
 */
KoiosApi::asset_fetchPolicyAssetAddresses(string $asset_policy, string $horizontal_filter = null);

/*
 * Get the information for all assets under the same policy
 *
 * GET /policy_asset_info
 *
 * @param string asset_policy
 * @param array horizontal_filter (optional)
 * @return Collection<PolicyAssetInformation>
 */
KoiosApi::asset_fetchPolicyAssetInfo(string $asset_policy, array $horizontal_filter = null);

/*
 * Get the list of asset under the given policy (including balances)
 *
 * GET /policy_asset_list
 *
 * @param string asset_policy
 * @param array horizontal_filter (optional)
 * @return Collection<PolicyAssetList>
 */
KoiosApi::asset_fetchPolicyAssetList(string $asset_policy, array $horizontal_filter = null);

/*
 * Get the summary of an asset (total transactions exclude minting/total wallets include only wallets with asset
 * balance)
 *
 * GET /asset_summary
 *
 * @param string asset_policy
 * @param string asset_name (optional)
 * @param array horizontal_filter (optional)
 * @return Collection<AssetSummary>
 */
KoiosApi::asset_fetchAssetSummary(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null);

/*
 * Get the list of current or all asset transaction hashes (newest first)
 *
 * GET /asset_txs
 *
 * @param string asset_policy
 * @param string asset_name (optional)
 * @param string after_block_height (optional)
 * @param string history (optional)
 * @param array horizontal_filter (optional)
 * @return Collection<AssetTransactions>
 */
KoiosApi::asset_fetchAssetTxs(string $asset_policy, string  $asset_name = null, int $after_block_height = null, string $history = null, array $horizontal_filter = null);
```
#### Pool
```php
/*
 * A list of all currently registered/retiring (not retired) pools
 *
 * GET /pool_list
 *
 * @param array horizontal_filter (optional)
 * @return Collection<PoolList>
 */
KoiosApi::pool_fetchPoolList(array $horizontal_filter = null);

/*
 * Current pool statuses and details for a specified list of pool ids
 *
 * POST /pool_info
 *
 * @param array pool_bech32_ids
 * @return Collection<PoolInfo>
 */
KoiosApi::pool_fetchPoolInfo(array $pool_bech32_ids);

/*
 * Returns Mark, Set and Go stake snapshots for the selected pool, useful for leaderlog calculation
 *
 * GET /pool_stake_snapshot
 *
 * @param string pool_bech32
 * @param array horizontal_filter (optional)
 * @return Collection<PoolStakeSnapshot>
 */
KoiosApi::pool_fetchPoolStakeSnapshot(string $pool_bech32, array $horizontal_filter = null);

/*
 * Return information about live delegators for a given pool.
 *
 * GET /pool_delegators
 *
 * @param string pool_bech32
 * @param array horizontal_filter (optional)
 * @return Collection<PoolStakeSnapshot>
 */
KoiosApi::pool_fetchPoolDelegators(string $pool_bech32, array $horizontal_filter = null);

/*
 * Return information about active delegators (incl. history) for a given pool and epoch number (all epochs if not
 * specified).
 *
 * GET /pool_delegators_history
 *
 * @param string pool_bech32
 * @param string epoch_no (optional)
 * @param array horizontal_filter (optional)
 * @return Collection<PoolDelegatorHistory>
 */
KoiosApi::pool_fetchDelegatorsHistory(string $pool_bech32, string $epoch_no = null, array $horizontal_filter = null);

/*
 * Return information about blocks minted by a given pool for all epochs (or _epoch_no if provided)
 *
 * GET /pool_blocks
 *
 * @param string pool_bech32
 * @param string epoch_no (optional)
 * @param array horizontal_filter (optional)
 * @return Collection<PoolBlocks>
 */
KoiosApi::pool_fetchPoolBlocks(string $pool_bech32, string $epoch_no = null, array $horizontal_filter = null);

/*
 * Return information about pool stake, block and reward history in a given epoch _epoch_no (or all epochs that pool
 * existed for, in descending order if no _epoch_no was provided)
 *
 * GET /pool_history
 *
 * @param string pool_bech32
 * @param string epoch_no (optional)
 * @param array horizontal_filter (optional)
 * @return Collection<PoolHistory>
 */
KoiosApi::pool_fetchPoolHistory(string $pool_bech32, string $epoch_no = null, array $horizontal_filter = null);

/*
 * Return all pool updates for all pools or only updates for specific pool if specified
 *
 * GET /pool_updates
 *
 * @param string pool_bech32
 * @param array horizontal_filter (optional))
 * @return Collection<PoolUpdates>
 */
KoiosApi::pool_fetchPoolUpdates(string $pool_bech32, array $horizontal_filter = null);

/*
 * A list of registered relays for all currently registered/retiring (not retired) pools
 *
 * GET /pool_relays
 *
 * @param array horizontal_filter (optional)
 * @return Collection<PoolRelays>
 */
KoiosApi::pool_fetchPoolRelays(array $horizontal_filter = null);

/*
 * Metadata (on & off-chain) for all currently registered/retiring (not retired) pools
 *
 * POST /pool_metadata
 *
 * @param array pool_bech32_ids
 * return Collection<PoolMetadata>
 */
KoiosApi::pool_fetchPoolMetadata(array $pool_bech32_ids);
```
#### Script
```php
/*
 * List of all existing native script hashes along with their creation transaction hashes
 *
 * GET /native_script_list
 *
 * @param array horizontal_filter (optional)
 * @return Collection<NativeScriptList>
 */
KoiosApi::script_fetchNativeScriptList(array $horizontal_filter = null);

/*
 * List of all existing Plutus script hashes along with their creation transaction hashes
 *
 * GET /plutus_script_list
 *
 * @param array horizontal_filter (optional)
 * @return Collection<PlutusScriptList>
 */
KoiosApi::script_fetchPlutusScriptList(array $horizontal_filter = null);

/*
 * List of all redeemers for a given script hash
 *
 * GET /script_redeemers
 *
 * @param string script_hash
 * @param array horizontal_filter (optional)
 * @return Collection<ScriptRedeemer>
 */
KoiosApi::script_fetchScriptRedeemers(string $script_hash, array $horizontal_filter = null);

/*
 * List of datum information for given datum hashes
 *
 * POST /datum_info
 *
 * @param array datum_hashes
 * @return Collection<DatumInformation>
 */
KoiosApi::script_fetchDatumInfo(array $datum_hashes);
```
#### Stake Account
```php
/*
 * Get a list of all stake addresses that have at least 1 transaction
 *
 * GET /account_list
 *
 * @param array horizontal_filter (optional)
 * @return Collection<AccountList>
 */
KoiosApi::account_fetchAccountList(array $horizontal_filter = null);

/*
 * Get the account information for given stake addresses
 *
 * POST /account_info
 *
 * @param array stake_addresses
 * @return Collection<AccountInformation>
 */
KoiosApi::account_fetchAccountInfos(array $stake_addresses);

/*
 * Get a list of all UTxOs for a given stake address (account)
 *
 * GET /account_utxos
 *
 * @param string stake_address
 * @param array horizontal_filter (optional)
 * @return Collection<AccountTransactions>
 */
KoiosApi::account_fetchAccountUtxos(string $stake_address, array $horizontal_filter = null);

/*
 * Get the cached account information for given stake addresses, effective for registered accounts
 *
 * POST /account_info_cached
 *
 * @param array stake_addresses
 * @return Collection<AccountInformation>
 */
KoiosApi::account_fetchAccountInfoCached(array $stake_addresses);

/*
 * Get the full rewards history (including MIR) for given stake addresses
 *
 * POST /account_rewards
 *
 * @param array stake_addresses
 * @return Collection<AccountInformation>
 */
KoiosApi::account_fetchAccountRewards(array $stake_addresses);

/*
 * Get the account updates (registration, deregistration, delegation and withdrawals) for given stake addresses
 *
 * POST /account_updates
 *
 * @param array stake_addresses
 * @return Collection<AccountUpdates>
 */
KoiosApi::account_fetchAccountUpdates(array $stake_addresses);

/*
 * Get all addresses associated with given staking accounts
 *
 * POST /account_addresses
 *
 * @param array stake_addresses
 * @return Collection<AccountAddresses>
 */
KoiosApi::account_fetchAccountAddresses(array $stake_addresses);

/*
 * Get the native asset balance for a given stake address
 *
 * POST /account_assets
 *
 * @param array stake_addresses
 * @return Collection<AccountAssets>
 */
KoiosApi::account_fetchAccountAssets(array $stake_addresses);

/*
 * Get the staking history of given stake addresses (accounts)
 *
 * POST /account_history
 *
 * @param array stake_addresses
 * @return Collection<AccountHistory>
 */
KoiosApi::account_fetchAccountHistory(array $stake_addresses);
```
## Bugs and Suggestions

## Copyright and License

[MIT](https://choosealicense.com/licenses/mit/)
