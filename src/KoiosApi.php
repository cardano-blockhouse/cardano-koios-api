<?php

namespace CardanoBlockhouse\CardanoKoiosApi;

use CardanoBlockhouse\CardanoKoiosApi\Api\Address\AddressInformation;
use CardanoBlockhouse\CardanoKoiosApi\Api\Address\AddressTransactions;
use CardanoBlockhouse\CardanoKoiosApi\Api\Asset\PolicyAssetInformation;
use CardanoBlockhouse\CardanoKoiosApi\Api\Epoch\EpochBlockProtocols;
use CardanoBlockhouse\CardanoKoiosApi\Api\Epoch\EpochInfo;
use CardanoBlockhouse\CardanoKoiosApi\Api\Epoch\EpochParams;
use CardanoBlockhouse\CardanoKoiosApi\Api\Network\NetworkGenesis;
use CardanoBlockhouse\CardanoKoiosApi\Api\Network\NetworkParamUpdates;
use CardanoBlockhouse\CardanoKoiosApi\Api\Network\NetworkTip;
use CardanoBlockhouse\CardanoKoiosApi\Api\Network\NetworkTotals;
use CardanoBlockhouse\CardanoKoiosApi\Api\Pool\PoolBlocks;
use CardanoBlockhouse\CardanoKoiosApi\Api\Pool\PoolDelegatorHistory;
use CardanoBlockhouse\CardanoKoiosApi\Api\Pool\PoolDelegatorList;
use CardanoBlockhouse\CardanoKoiosApi\Api\Pool\PoolHistory;
use CardanoBlockhouse\CardanoKoiosApi\Api\Pool\PoolInfo;
use CardanoBlockhouse\CardanoKoiosApi\Api\Pool\PoolList;
use CardanoBlockhouse\CardanoKoiosApi\Api\Pool\PoolMetadata;
use CardanoBlockhouse\CardanoKoiosApi\Api\Pool\PoolRelays;
use CardanoBlockhouse\CardanoKoiosApi\Api\Pool\PoolStakeSnapshot;
use CardanoBlockhouse\CardanoKoiosApi\Api\Pool\PoolUpdates;
use CardanoBlockhouse\CardanoKoiosApi\Api\Transactions\TransactionInfos;
use CardanoBlockhouse\CardanoKoiosApi\Api\Transactions\TransactionMetadata;
use CardanoBlockhouse\CardanoKoiosApi\Api\Transactions\TransactionMetadataLabels;
use CardanoBlockhouse\CardanoKoiosApi\Api\Transactions\TransactionStatus;
use Illuminate\Support\Facades\Http;
use function PHPUnit\Framework\isNull;

class KoiosApi
{
    private $network = '';
    private $baseUrl = '';

    private const MAINNET_URL = 'https://api.koios.rest/api/v0';
    private const PREPROD_URL = 'https://proprod.koios.rest/api/v0';
    private const PREVIEW_URL = 'https://preview.koios.rest/api/v0';

    private const KOIOS_API_LIMIT = 500;
    private const KOIOS_OFFSET_START = 0;
    private const KOIOS_COUNT_START = 1;

    private $limiter;

    public function __construct() {
        $this->network = 'mainnet';
        $this->baseUrl = self::MAINNET_URL;

        $this->limiter = Limiter::create();
    }

    public function setNetwork($network) {
        $this->network = $network;

        switch ($network) {
            case 'mainnet':
                $this->baseUrl = self::MAINNET_URL;
                break;
            case 'preprod':
                $this->baseUrl = self::PREPROD_URL;
                break;
            case 'preview':
                $this->baseUrl = self::PREVIEW_URL;
                break;
        }
    }

    // Requests

    private function getRequest(string $endpoint, array $params = null, $limit = null, $offset = null) {
        if (!is_null($params) && count($params) > 0) {
            $endpoint = $this->addGetQueryString($endpoint, $params);
        }

        if(isset($limit) && isset($offset)) {
            if (!is_null($params) && count($params) > 0) {
                $endpoint .= '&limit=' . $limit . '&offset=' . $offset;
            } else {
                $endpoint .= '?limit=' . $limit . '&offset=' . $offset;
            }
        }

        $this->limiter->nextRequest();
        return HTTP::retry(5, 100)->timeout(5)->get($this->baseUrl.$endpoint);

    }

    private function postRequest(string $endpoint, array $postParams) {
        $this->limiter->nextRequest();
        return Http::retry(5, 100)->timeout(5)->post($this->baseUrl.$endpoint, $postParams);
    }

    private function addGetQueryString(string $endpointUrl, array $params = null) {
        $endpointUrl .= '?';
        foreach ($params as $param) {
            $endpointUrl .= $param.'&';
        }
        return substr($endpointUrl, 0, -1);
    }

    private function addPostParams() {}

    // Address /////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Get address info - balance, associated stake address (if any) and UTxO set for given addresses
     *
     * POST /address_info
     *
     * @param array addresses
     * @return Collection<AddressInformation>
     */
    public function address_fetchAddressInfo(array $addresses) {
        $postParams = [];
        $postParams['_addresses'] = $addresses;
        $response = $this->postRequest('/address_info', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = AddressInformation::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get the transaction hash list of input address array, optionally filtering after specified block height
     * (inclusive)
     *
     * POST /address_txs
     *
     * @param array addresses
     * @return Collection<AddressTransactions>
     */
    public function address_fetchAddressTxs(array $addresses) {
        $postParams = [];
        $postParams['_addresses'] = $addresses;
        $response = $this->postRequest('/address_txs', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = AddressTransactions::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get a list of UTxO against input payment credential array including their balances
     *
     * POST /credential_utxos
     */
    public function address_fetchCredentialUtxos() {}

    /*
     * Get the list of all the assets (policy, name and quantity) for given addresses
     *
     * POST /address_assets
     */
    public function address_fetchAddressAssets() {}

    /*
     * Get the transaction hash list of input payment credential array, optionally filtering after specified block
     * height (inclusive)
     *
     * POST /credential_txs
     */
    public function address_fetchCredentialTxs() {}

    // Asset ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Get the list of all native assets (paginated)
     *
     * GET /asset_list
     */
    public function asset_fetchAssetList() {}

    /*
     * Get a list of assets registered via token registry on github
     *
     * GET /asset_token_registry
     */
    public function asset_fetchAssetTokenRegistry() {}

    /*
     * Get the list of all addresses holding a given asset
     *
     * Note - Due to cardano's UTxO design and usage from projects, asset to addresses map can be infinite. Thus, for a
     * small subset of active projects with millions of transactions, these might end up with timeouts (HTTP code 504)
     * on free layer. Such large-scale projects are free to subscribe to query layers to have a dedicated cache table
     * for themselves served via Koios.
     *
     * GET /asset_addresses
     */
    public function asset_fetchAssetAddresses() {}

    /*
     * Get the address where specified NFT currently reside on.
     *
     * GET /asset_nft_address
     */
    public function asset_fetchAssetNftAddress() {}

    /*
     * Get the information of an asset including first minting & token registry metadata
     *
     * GET /asset_info
     */
    public function asset_fetchAssetInfo() {

    }

    /*
     * Get the information of a list of assets including first minting & token registry metadata
     *
     * POST /asset_info
     */
    public function asset_fetchAssetInfoBulk() {

    }

    /*
     * Get the mint/burn history of an asset
     *
     * GET /asset_history
     */
    public function asset_fetchAssetHistory() {

    }

    /*
     * Get the list of addresses with quantity for each asset on the given policy
     *
     * Note - Due to cardano's UTxO design and usage from projects, asset to addresses map can be infinite. Thus, for a
     * small subset of active projects with millions of transactions, these might end up with timeouts (HTTP code 504)
     * on free layer. Such large-scale projects are free to subscribe to query layers to have a dedicated cache table
     * for themselves served via Koios.
     *
     * GET /policy_asset_addresses
     */
    public function asset_fetchPolicyAssetAddresses() {

    }

    /*
     * Get the information for all assets under the same policy
     *
     * GET /policy_asset_info
     *
     * @param string asset_policy
     * @return Collection<PolicyAssetInformation>
     */
    public function asset_fetchPolicyAssetInfo(string $asset_policy) {
        $params[] = '_asset_policy=' . $asset_policy;
        $response = $this->getRequest('/policy_asset_info', $params);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = PolicyAssetInformation::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get the list of asset under the given policy (including balances)
     *
     * GET /policy_asset_list
     */
    public function asset_fetchPolicyAssetList() {

    }

    /*
     * Get the summary of an asset (total transactions exclude minting/total wallets include only wallets with asset
     * balance)
     *
     * GET /asset_summary
     */
    public function asset_fetchAssetSummary() {

    }

    /*
     * Get the list of current or all asset transaction hashes (newest first)
     *
     * GET /asset_txs
     */
    public function asset_fetchAssetTxs() {

    }

    // Epoch ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Get the epoch information, all epochs if no epoch specified
     *
     * GET /epoch_info
     *
     * @param epoch_no (optional)
     * @param include_next_epoch (optional)
     * @return Collection<EpochInfo>
     */
    public function epoch_fetchEpochInfo(string $epoch_no = null, string $include_next_epoch = null) {
        $params = null;
        if (!is_null($epoch_no)) {
            $params[] = '_epoch_no=' . $epoch_no;
        }
        if (!is_null($include_next_epoch)) {
            $params[] = '_include_next_epoch=' . $include_next_epoch;
        }

        $response = $this->getRequest('/epoch_info', $params);

        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = EpochInfo::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get the protocol parameters for specific epoch, returns information about all epochs if no epoch specified
     *
     * GET /epoch_params
     *
     * @param epoch_no (optional)
     * @return Collection<EpochParams>
     */
    public function epoch_fetchEpochParams(string $epoch_no = null) {
        $params = null;
        if (!is_null($epoch_no)) {
            $params[] = '_epoch_no=' . $epoch_no;
        }

        $response = $this->getRequest('/epoch_params', $params);

        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = EpochParams::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get the information about block protocol distribution in epoch
     *
     * GET /epoch_block_protocols
     *
     * @param epoch_no (optional)
     * @return Collection<EpochBlockProtocols>
     */
    public function epoch_fetchEpochBlockProtocols(string $epoch_no = null) {
        $params = null;
        if (!is_null($epoch_no)) {
            $params[] = '_epoch_no=' . $epoch_no;
        }

        $response = $this->getRequest('/epoch_block_protocols', $params);

        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = EpochBlockProtocols::from($item);
        }
        return collect($returnArray);
    }

    // Network /////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Get the tip info about the latest block seen by chain
     *
     * GET /tip
     *
     * @return NetworkTip
     */
    public function network_fetchTip() {
        $response = $this->getRequest('/tip');
        return NetworkTip::from(json_decode($response)[0]);
    }

    /*
     * Get the Genesis parameters used to start specific era on chain
     *
     * GET /genesis
     *
     * @return NetworkGenesis
     */
    public function network_fetchGenesis() {
        $response = $this->getRequest('/genesis');
        return NetworkGenesis::from(json_decode($response)[0]);
    }

    /*
     * Get the circulating utxo, treasury, rewards, supply and reserves in lovelace for specified epoch, all epochs if
     * empty
     *
     * GET /totals
     *
     * @param string epoch_no
     * @return Collection<NetworkTotals>
     */
    public function network_fetchTotals(string $epoch_no = null, string $filter = null) {
        $params = null;
        if (!is_null($epoch_no)) {
            $params[] = '_epoch_no=' . $epoch_no;
        }
        if (!is_null($filter)) {
            $params[] = $filter;
        }
        $response = $this->getRequest('/totals', $params);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = NetworkTotals::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get all parameter update proposals submitted to the chain starting Shelley era
     *
     * GET /param_updates
     *
     * @return NetworkParamUpdates
     */
    public function network_fetchParamUpdates() {
        $response = $this->getRequest('/param_updates');
        return NetworkParamUpdates::from(json_decode($response));
    }

    // Pool ////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * A list of all currently registered/retiring (not retired) pools
     *
     * GET /pool_list
     *
     * @return Collection<PoolList>
     */
    public function pool_fetchPoolList() {
        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $pools = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($pools > 0) {

            $response = $this->getRequest('/pool_list', null, $limit, $offset);
            $poolInfos = (array) json_decode($response);
            $pools = count($poolInfos);

            foreach ($poolInfos as $item) {
                $returnArray[] = PoolList::from($item);
            }

            $offset = $offset + $pools;
        }
        return collect($returnArray);
    }

    /*
     * Current pool statuses and details for a specified list of pool ids
     *
     * POST /pool_info
     *
     * @param array pool_bech32_ids
     * @return Collection<PoolInfo>
     */
    public function pool_fetchPoolInfo(array $pool_bech32_ids) {
        $postParams = [];
        $postParams['_pool_bech32_ids'] = $pool_bech32_ids;
        $response = $this->postRequest('/pool_info', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response, true) as $item) {
            $returnArray[] = PoolInfo::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Returns Mark, Set and Go stake snapshots for the selected pool, useful for leaderlog calculation
     *
     * GET /pool_stake_snapshot
     *
     * @param string pool_bech32
     * @return Collection<PoolStakeSnapshot>
     */
    public function pool_fetchPoolStakeSnapshot(string $pool_bech32) {
        $params[] = '_pool_bech32='.$pool_bech32;
        $response = $this->getRequest('/pool_stake_snapshot', $params);
        $returnArray = [];
        foreach ((array) json_decode($response, true) as $item) {
            $returnArray[] = PoolStakeSnapshot::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Return information about live delegators for a given pool.
     *
     * GET /pool_delegators
     *
     * @param string pool_bech32
     * @return Collection<PoolStakeSnapshot>
     */
    public function pool_fetchPoolDelegators(string $pool_bech32) {
        $params[] = '_pool_bech32='.$pool_bech32;

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $delegators = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($delegators > 0) {

            $response = $this->getRequest('/pool_delegators', $params, $limit, $offset);
            $delegatorsArray = (array) json_decode($response);
            $delegators = count($delegatorsArray);

            foreach ($delegatorsArray as $item) {
                $returnArray[] = PoolDelegatorList::from($item);
            }

            $offset = $offset + $delegators;
        }
        return collect($returnArray);
    }

    /*
     * Return information about active delegators (incl. history) for a given pool and epoch number (all epochs if not
     * specified).
     *
     * GET /pool_delegators_history
     *
     * @param string pool_bech32
     * @param string epoch_no (optional)
     * @return Collection<PoolDelegatorHistory>
     */
    public function pool_fetchDelegatorsHistory(string $pool_bech32, string $epoch_no = null) {
        $params[] = '_pool_bech32='.$pool_bech32;

        if ($epoch_no) {
            $params[] = '_epoch_no='.$epoch_no;
        }

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $delegatorHistories = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($delegatorHistories > 0) {

            $response = $this->getRequest('/pool_delegators_history', $params, $limit, $offset);
            $delegatorHistoryArray = (array) json_decode($response);
            $delegatorHistories = count($delegatorHistoryArray);

            foreach ($delegatorHistoryArray as $item) {
                $returnArray[] = PoolDelegatorHistory::from($item);
            }

            $offset = $offset + $delegatorHistories;
        }
        return collect($returnArray);
    }

    /*
     * Return information about blocks minted by a given pool for all epochs (or _epoch_no if provided)
     *
     * GET /pool_blocks
     *
     * @param string pool_bech32
     * @param string epoch_no (optional)
     * @return Collection<PoolBlocks>
     */
    public function pool_fetchPoolBlocks(string $pool_bech32, string $epoch_no = null) {
        $params[] = '_pool_bech32='.$pool_bech32;

        if ($epoch_no) {
            $params[] = '_epoch_no='.$epoch_no;
        }

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $poolBlocks = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($poolBlocks > 0) {

            $response = $this->getRequest('/pool_blocks', $params, $limit, $offset);
            $poolBlocksArray = (array) json_decode($response);
            $poolBlocks = count($poolBlocksArray);

            foreach ($poolBlocksArray as $item) {
                $returnArray[] = PoolBlocks::from($item);
            }

            $offset = $offset + $poolBlocks;
        }
        return collect($returnArray);
    }

    /*
     * Return information about pool stake, block and reward history in a given epoch _epoch_no (or all epochs that pool
     * existed for, in descending order if no _epoch_no was provided)
     *
     * GET /pool_history
     *
     * @param string pool_bech32
     * @param string epoch_no (optional)
     * @return Collection<PoolHistory>
     */
    public function pool_fetchPoolHistory(string $pool_bech32, string $epoch_no = null) {
        $params[] = '_pool_bech32='.$pool_bech32;

        if ($epoch_no) {
            $params[] = '_epoch_no='.$epoch_no;
        }

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $poolHistories = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($poolHistories > 0) {

            $response = $this->getRequest('/pool_history', $params, $limit, $offset);
            $poolHistoryArray = (array) json_decode($response);
            $poolHistories = count($poolHistoryArray);

            foreach ($poolHistoryArray as $item) {
                $returnArray[] = PoolHistory::from($item);
            }

            $offset = $offset + $poolHistories;
        }
        return collect($returnArray);
    }

    /*
     * Return all pool updates for all pools or only updates for specific pool if specified
     *
     * GET /pool_updates
     *
     * @param string pool_bech32
     * @return Collection<PoolUpdates>
     */
    public function pool_fetchPoolUpdates(string $pool_bech32) {
        $params[] = '_pool_bech32='.$pool_bech32;

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $poolUpdates = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($poolUpdates > 0) {

            $response = $this->getRequest('/pool_updates', $params, $limit, $offset);
            $poolUpdatesArray = (array) json_decode($response);
            $poolUpdates = count($poolUpdatesArray);

            foreach ($poolUpdatesArray as $item) {
                $returnArray[] = PoolUpdates::from($item);
            }

            $offset = $offset + $poolUpdates;
        }
        return collect($returnArray);
    }

    /*
     * A list of registered relays for all currently registered/retiring (not retired) pools
     *
     * GET /pool_relays
     *
     * @return Collection<PoolRelays>
     */
    public function pool_fetchPoolRelays() {
        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $poolRelays = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($poolRelays > 0) {

            $response = $this->getRequest('/pool_relays', null, $limit, $offset);
            $poolRelaysArray = (array) json_decode($response);
            $poolRelays = count($poolRelaysArray);

            foreach ($poolRelaysArray as $item) {
                $returnArray[] = PoolRelays::from($item);
            }

            $offset = $offset + $poolRelays;
        }
        return collect($returnArray);
    }

    /*
     * Metadata (on & off-chain) for all currently registered/retiring (not retired) pools
     *
     * POST /pool_metadata
     *
     * @param array pool_bech32_ids
     * return Collection<PoolMetadata>
     */
    public function pool_fetchPoolMetadata(array $pool_bech32_ids) {
        $postParams = [];
        $postParams['_pool_bech32_ids'] = $pool_bech32_ids;
        $response = $this->postRequest('/pool_metadata', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response, true) as $item) {
            $returnArray[] = PoolMetadata::from($item);
        }
        return collect($returnArray);
    }

    // Script //////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * List of all existing native script hashes along with their creation transaction hashes
     *
     * GET /native_script_list
     */
    public function script_fetchNativeScriptList() {}

    /*
     * List of all existing Plutus script hashes along with their creation transaction hashes
     *
     * GET /plutus_script_list
     */
    public function script_fetchPlutusScriptList() {}

    /*
     * List of all redeemers for a given script hash
     *
     * GET /script_redeemers
     */
    public function script_fetchScriptRedeemers() {}

    /*
     * List of datum information for given datum hashes
     *
     * POST /datum_info
     */
    public function script_fetchDatumInfo() {}

    // Stake Account ///////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Get a list of all stake addresses that have atleast 1 transaction
     *
     * GET /account_list
     */
    public function account_fetchAccountList() {}

    /*
     * Get the account information for given stake addresses
     *
     * POST /account_info
     */
    public function account_fetchAccountInfos() {}

    /*
     * Get a list of all UTxOs for a given stake address (account)
     *
     * GET /account_utxos
     */
    public function account_fetchAccountUtxos() {}

    /*
     * Get the cached account information for given stake addresses, effective for registered accounts
     *
     * POST /account_info_cached
     */
    public function account_fetchAccountInfoCached() {}

    /*
     * Get the full rewards history (including MIR) for given stake addresses
     *
     * POST /account_rewards
     */
    public function account_fetchAccountRewards() {}

    /*
     * Get the account updates (registration, deregistration, delegation and withdrawals) for given stake addresses
     *
     * POST /account_updates
     */
    public function account_fetchAccountUpdates() {}

    /*
     * Get all addresses associated with given staking accounts
     *
     * POST /account_addresses
     */
    public function account_fetchAccountAddresses() {}

    /*
     * Get the native asset balance for a given stake address
     *
     * POST /account_assets
     */
    public function account_fetchAccountAssets() {}

    /*
     * Get the staking history of given stake addresses (accounts)
     *
     * POST /account_history
     */
    public function account_fetchAccountHistory() {}


    // Transactions /////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Get detailed information about transaction(s)
     *
     * POST /tx_info
     *
     * @param array tx_hashes
     * @return Collection<TransactionInfos>
     */
    public function transaction_fetchTransactionInfos(array $tx_hashes) {
        $postParams = [];
        $postParams['_tx_hashes'] = $tx_hashes;
        $response = $this->postRequest('/tx_info', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = TransactionInfos::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get metadata information (if any) for given transaction(s)
     *
     * POST /tx_metadata
     *
     * @param array tx_hashes
     * @return
     */
    public function transaction_fetchTransactionMetadata(array $tx_hashes) {
        $postParams = [];
        $postParams['_tx_hashes'] = $tx_hashes;
        $response = $this->postRequest('/tx_metadata', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = TransactionMetadata::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get a list of all transaction metalabels
     *
     * GET /tx_metalabels
     *
     * @return Collection<TransactionMetadataLabels>
     */
    public function transaction_fetchTransactionMetadataLabels() {
        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $transactionMetadata = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($transactionMetadata > 0) {

            $response = $this->getRequest('/tx_metalabels', null, $limit, $offset);
            $transactionMetadataArray = (array) json_decode($response);
            $transactionMetadata = count($transactionMetadataArray);

            foreach ($transactionMetadataArray as $item) {
                $returnArray[] = TransactionMetadataLabels::from($item);
            }

            $offset = $offset + $transactionMetadata;
        }
        return collect($returnArray);
    }

    /*
     * Submit an already serialized transaction to the network.
     *
     * POST /submittx
     */
    public function transaction_submitTransaction() {
        // TODO
    }

    /*
     * Get the number of block confirmations for a given transaction hash list
     *
     * POST /tx_status
     */
    public function transaction_fetchTransactionStatus(array $tx_hashes) {
        $postParams = [];
        $postParams['_tx_hashes'] = $tx_hashes;
        $response = $this->postRequest('/tx_status', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = TransactionStatus::from($item);
        }
        return collect($returnArray);
    }
}
