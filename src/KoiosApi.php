<?php

namespace CardanoBlockhouse\CardanoKoiosApi;

use CardanoBlockhouse\CardanoKoiosApi\Api\Address\AddressAssets;
use CardanoBlockhouse\CardanoKoiosApi\Api\Address\AddressInformation;
use CardanoBlockhouse\CardanoKoiosApi\Api\Address\AddressTransactions;
use CardanoBlockhouse\CardanoKoiosApi\Api\Address\CredentialTransactions;
use CardanoBlockhouse\CardanoKoiosApi\Api\Address\CredentialUtxos;
use CardanoBlockhouse\CardanoKoiosApi\Api\Asset\AssetAddresses;
use CardanoBlockhouse\CardanoKoiosApi\Api\Asset\AssetHistory;
use CardanoBlockhouse\CardanoKoiosApi\Api\Asset\AssetInfo;
use CardanoBlockhouse\CardanoKoiosApi\Api\Asset\AssetList;
use CardanoBlockhouse\CardanoKoiosApi\Api\Asset\AssetNFTAddress;
use CardanoBlockhouse\CardanoKoiosApi\Api\Asset\AssetSummary;
use CardanoBlockhouse\CardanoKoiosApi\Api\Asset\AssetTokenRegistry;
use CardanoBlockhouse\CardanoKoiosApi\Api\Asset\AssetTransactions;
use CardanoBlockhouse\CardanoKoiosApi\Api\Asset\PolicyAssetInformation;
use CardanoBlockhouse\CardanoKoiosApi\Api\Asset\PolicyAssetList;
use CardanoBlockhouse\CardanoKoiosApi\Api\Block\BlockInformation;
use CardanoBlockhouse\CardanoKoiosApi\Api\Block\BlockList;
use CardanoBlockhouse\CardanoKoiosApi\Api\Block\BlockTransactions;
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
use CardanoBlockhouse\CardanoKoiosApi\Api\Script\DatumInformation;
use CardanoBlockhouse\CardanoKoiosApi\Api\Script\NativeScriptList;
use CardanoBlockhouse\CardanoKoiosApi\Api\Script\PlutusScriptList;
use CardanoBlockhouse\CardanoKoiosApi\Api\Script\ScriptRedeemer;
use CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount\AccountAddresses;
use CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount\AccountAssets;
use CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount\AccountHistory;
use CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount\AccountInformation;
use CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount\AccountList;
use CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount\AccountRewards;
use CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount\AccountTransactions;
use CardanoBlockhouse\CardanoKoiosApi\Api\StakeAccount\AccountUpdates;
use CardanoBlockhouse\CardanoKoiosApi\Api\Transactions\TransactionInfos;
use CardanoBlockhouse\CardanoKoiosApi\Api\Transactions\TransactionMetadata;
use CardanoBlockhouse\CardanoKoiosApi\Api\Transactions\TransactionMetadataLabels;
use CardanoBlockhouse\CardanoKoiosApi\Api\Transactions\TransactionStatus;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\StreamInterface;

class KoiosApi
{
    private $network = '';
    private $baseUrl = '';

    private const MAINNET_URL = 'https://api.koios.rest/api/v0';
    private const PREPROD_URL = 'https://preprod.koios.rest/api/v0';
    private const PREVIEW_URL = 'https://preview.koios.rest/api/v0';

    private const KOIOS_API_LIMIT = 500;
    private const KOIOS_OFFSET_START = 0;
    private const KOIOS_COUNT_START = 1;

    private $limiter;

    public function __construct(string $network = 'mainnet') {
        $this->setNetwork($network);
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

    private function getRequest(string $endpoint, array $params = null, $limit = null, $offset = null, array $horizontal_filters = null, array $vertical_filters = null) {
        if (!is_null($params) && count($params) > 0) {
            $endpoint = $this->addGetQueryString($endpoint, $params);
        }

        if(isset($limit) && isset($offset)) {
            if (isset($params) && count($params) > 0) {
                $endpoint .= '&limit=' . $limit . '&offset=' . $offset;
            } else {
                $endpoint .= '?limit=' . $limit . '&offset=' . $offset;
            }
        }

        if(isset($horizontal_filters)) {
            if (isset($params) && count($params) > 0 || (isset($limit) && isset($offset))) {
                $endpoint .= '&';
            } else {
                $endpoint .= '?';
            }
            foreach ($horizontal_filters as $horizontal_filter) {
                $endpoint .= $horizontal_filter.'&';
            }
            $endpoint = substr($endpoint, 0, -1);
        }

        if(isset($vertical_filters)) {
            $vertical_filter_string = 'select=';
            foreach ($vertical_filters as $vertical_filter) {
                $vertical_filter_string .= $vertical_filter.',';
            }
            $vertical_filter_string = substr($vertical_filter_string, 0, -1);
            if (isset($params) && count($params) > 0 || (isset($limit) && isset($offset)) || isset($horizontal_filters)) {
                $endpoint .= '&'.$vertical_filter_string;
            } else {
                $endpoint .= '?'.$vertical_filter_string;
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
     * @param int after_block_height (optional)
     * @return Collection<AddressTransactions>
     */
    public function address_fetchAddressTxs(array $addresses, int $after_block_height = null) {

        $postParams = [];
        $postParams['_addresses'] = $addresses;
        if($after_block_height) {
            $postParams['_after_block_height'] = $after_block_height;
        }
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
     *
     * @param array payment_credentials
     * @return Collection<CredentialUtxos>
     */
    public function address_fetchCredentialUtxos(array $payment_credentials) {
        $postParams = [];
        $postParams['_payment_credentials'] = $payment_credentials;
        $response = $this->postRequest('/credential_utxos', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = CredentialUtxos::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get the list of all the assets (policy, name and quantity) for given addresses
     *
     * POST /address_assets
     *
     * @param array addresses
     * @return Collection<AddressAssets>
     */
    public function address_fetchAddressAssets(array $addresses) {
        $postParams = [];
        $postParams['_addresses'] = $addresses;
        $response = $this->postRequest('/address_assets', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = AddressAssets::from($item);
        }
        return collect($returnArray);
    }

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
    public function address_fetchCredentialTxs(array $payment_credentials, int $after_blockheight = null) {
        $postParams = [];
        $postParams['_payment_credentials'] = $payment_credentials;
        if($after_blockheight) {
            $postParams['_after_block_height'] = $after_blockheight;
        }
        $response = $this->postRequest('/credential_txs', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = CredentialTransactions::from($item);
        }
        return collect($returnArray);
    }

    // Asset ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Get the list of all native assets (paginated)
     *
     * GET /asset_list
     *
     * @param array $horizontal_filter (optional)
     *
     * @return Collection<AssetList>
     */
    public function asset_fetchAssetList(array $horizontal_filter = null) {
        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $assets = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($assets > 0) {

            $response = $this->getRequest('/asset_list', null, $limit, $offset, $horizontal_filter);
            $assetsArray = (array) json_decode($response);
            $assets = count($assetsArray);

            foreach ($assetsArray as $item) {
                $returnArray[] = AssetList::from($item);
            }

            $offset = $offset + $assets;
        }
        return collect($returnArray);
    }

    /*
     * Get a list of assets registered via token registry on github
     *
     * GET /asset_token_registry
     *
     * @param array horizontal_filter (optional)
     * @return Collection<AssetTokenRegistry>
     */
    public function asset_fetchAssetTokenRegistry(array $horizontal_filter = null) {
        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $assetTokenRegistries = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($assetTokenRegistries > 0) {

            $response = $this->getRequest('/asset_token_registry', null, $limit, $offset, $horizontal_filter);
            $assetTokenRegistryArray = (array) json_decode($response);
            $assetTokenRegistries = count($assetTokenRegistryArray);

            foreach ($assetTokenRegistryArray as $item) {
                $returnArray[] = AssetTokenRegistry::from($item);
            }

            $offset = $offset + $assetTokenRegistries;
        }
        return collect($returnArray);
    }

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
    public function asset_fetchAssetAddresses(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null) {
        $params[] = '_asset_policy=' . $asset_policy;
        if($asset_name) {
            $params[] = '_asset_name=' . $asset_name;
        }

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $assetAddresses = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($assetAddresses > 0) {

            $response = $this->getRequest('/asset_addresses', $params, $limit, $offset, $horizontal_filter);
            $assetAddressesArray = (array) json_decode($response);
            $assetAddresses = count($assetAddressesArray);

            foreach ($assetAddressesArray as $item) {
                $returnArray[] = AssetAddresses::from($item);
            }

            $offset = $offset + $assetAddresses;
        }
        return collect($returnArray);
    }

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
    public function asset_fetchAssetNftAddress(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null) {
        $params[] = '_asset_policy=' . $asset_policy;
        if($asset_name) {
            $params[] = '_asset_name=' . $asset_name;
        }

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $assetNftAddresses = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($assetNftAddresses > 0) {

            $response = $this->getRequest('/asset_nft_address', $params, $limit, $offset, $horizontal_filter);
            $assetNftAddressesArray = (array) json_decode($response);
            $assetNftAddresses = count($assetNftAddressesArray);

            foreach ($assetNftAddressesArray as $item) {
                $returnArray[] = AssetNFTAddress::from($item);
            }

            $offset = $offset + $assetNftAddresses;
        }
        return collect($returnArray);
    }

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
    public function asset_fetchAssetInfo(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null) {
        $params[] = '_asset_policy=' . $asset_policy;
        if($asset_name) {
            $params[] = '_asset_name=' . $asset_name;
        }

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $assetInfos = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($assetInfos > 0) {

            $response = $this->getRequest('/asset_info', $params, $limit, $offset, $horizontal_filter);
            $assetInfoArray = (array) json_decode($response);
            $assetInfos = count($assetInfoArray);

            foreach ($assetInfoArray as $item) {
                $returnArray[] = AssetInfo::from($item);
            }

            $offset = $offset + $assetInfos;
        }
        return collect($returnArray);
    }

    /*
     * Get the information of a list of assets including first minting & token registry metadata
     *
     * POST /asset_info
     *
     * @param array asset_list
     * @return Collection<AssetList>
     */
    public function asset_fetchAssetInfoBulk(array $asset_list) {
        $postParams = [];
        $postParams['_asset_list'] = $asset_list;
        $response = $this->postRequest('/asset_info', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = AssetList::from($item);
        }
        return collect($returnArray);
    }

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
    public function asset_fetchAssetHistory(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null) {
        $params[] = '_asset_policy=' . $asset_policy;
        if($asset_name) {
            $params[] = '_asset_name=' . $asset_name;
        }

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $assetHistories = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($assetHistories > 0) {

            $response = $this->getRequest('/asset_history', $params, $limit, $offset, $horizontal_filter);
            $assetHistoryArray = (array) json_decode($response);
            $assetHistories = count($assetHistoryArray);

            foreach ($assetHistoryArray as $item) {
                $returnArray[] = AssetHistory::from($item);
            }

            $offset = $offset + $assetHistories;
        }
        return collect($returnArray);
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
     * @param array horizontal_filter (optional)
     * @return Collection<PolicyAssetInformation>
     */
    public function asset_fetchPolicyAssetInfo(string $asset_policy, array $horizontal_filter = null) {
        $params[] = '_asset_policy=' . $asset_policy;
        $response = $this->getRequest('/policy_asset_info', $params, null, null, $horizontal_filter);
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
     *
     * @param string asset_policy
     * @param array horizontal_filter (optional)
     * @return Collection<PolicyAssetList>
     */
    public function asset_fetchPolicyAssetList(string $asset_policy, array $horizontal_filter = null) {
        $params[] = '_asset_policy=' . $asset_policy;
        $response = $this->getRequest('/policy_asset_list', $params, null, null, $horizontal_filter);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = PolicyAssetList::from($item);
        }
        return collect($returnArray);
    }

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
    public function asset_fetchAssetSummary(string $asset_policy, string  $asset_name = null, array $horizontal_filter = null) {
        $params[] = '_asset_policy=' . $asset_policy;
        if($asset_name) {
            $params[] = '_asset_name=' . $asset_name;
        }

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $assetSummaries = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($assetSummaries > 0) {

            $response = $this->getRequest('/asset_summary', $params, $limit, $offset, $horizontal_filter);
            $assetSummariyArray = (array) json_decode($response);
            $assetSummaries = count($assetSummariyArray);

            foreach ($assetSummariyArray as $item) {
                $returnArray[] = AssetSummary::from($item);
            }

            $offset = $offset + $assetSummaries;
        }
        return collect($returnArray);
    }

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
    public function asset_fetchAssetTxs(string $asset_policy, string  $asset_name = null, int $after_block_height = null, string $history = null, array $horizontal_filter = null) {
        $params[] = '_asset_policy=' . $asset_policy;
        if($asset_name) {
            $params[] = '_asset_name=' . $asset_name;
        }
        if($after_block_height) {
            $params[] = '_after_block_height=' . $after_block_height;
        }
        if($history) {
            $params[] = 'history=' . $history;
        }

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $assetTransactions = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($assetTransactions > 0) {

            $response = $this->getRequest('/asset_txs', $params, $limit, $offset, $horizontal_filter);
            $assetTransactionArray = (array) json_decode($response);
            $assetTransactions = count($assetTransactionArray);

            foreach ($assetTransactionArray as $item) {
                $returnArray[] = AssetTransactions::from($item);
            }

            $offset = $offset + $assetTransactions;
        }
        return collect($returnArray);
    }
    // Block ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Get summarised details about all blocks (paginated - latest first)
     *
     * GET /blocks
     *
     * @param array horizontal_filter (optinal)
     * @return Collection<BlockList>
     */
    public function block_fetchBlocks(array $horizontal_filter = null) {

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $blocks = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($blocks > 0) {

            $response = $this->getRequest('/blocks', null, $limit, $offset, $horizontal_filter);
            $blocksArray = (array) json_decode($response);
            $blocks = count($blocksArray);

            foreach ($blocksArray as $item) {
                $returnArray[] = BlockList::from($item);
            }

            $offset = $offset + $blocks;
        }
        return collect($returnArray);
    }

    /*
     * Get detailed information about a specific block
     *
     * POST /block_info
     *
     * @param array block_hashes
     * @return Collection<BlockInformation>
     */
    public function block_fetchBlockInformation(array $block_hashes) {
        $postParams = [];
        $postParams['_block_hashes'] = $block_hashes;
        $response = $this->postRequest('/block_info', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = BlockInformation::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get a list of all transactions included in provided blocks
     *
     * POST /block_txs
     *
     * @param array block_hashes
     * @return Collection<BlockTransactions>
     */
    public function block_fetchBlockTransactions(array $block_hashes) {
        $postParams = [];
        $postParams['_block_hashes'] = $block_hashes;
        $response = $this->postRequest('/block_txs', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = BlockTransactions::from($item);
        }
        return collect($returnArray);
    }

    // Epoch ///////////////////////////////////////////////////////////////////////////////////////////////////////////

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
    public function epoch_fetchEpochInfo(string $epoch_no = null, string $include_next_epoch = null, array $horizontal_filter = null) {
        $params = null;
        if (!is_null($epoch_no)) {
            $params[] = '_epoch_no=' . $epoch_no;
        }
        if (!is_null($include_next_epoch)) {
            $params[] = '_include_next_epoch=' . $include_next_epoch;
        }

        $response = $this->getRequest('/epoch_info', $params, null, null, $horizontal_filter);

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
     * @param array horizontal_filter (optinal)
     * @return Collection<EpochParams>
     */
    public function epoch_fetchEpochParams(string $epoch_no = null, array $horizontal_filter = null) {
        $params = null;
        if (!is_null($epoch_no)) {
            $params[] = '_epoch_no=' . $epoch_no;
        }

        $response = $this->getRequest('/epoch_params', $params, null, null, $horizontal_filter);

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
     * @param array horizontal_filter (optional)
     * @return Collection<EpochBlockProtocols>
     */
    public function epoch_fetchEpochBlockProtocols(string $epoch_no = null, array $horizontal_filter = null) {
        $params = null;
        if (!is_null($epoch_no)) {
            $params[] = '_epoch_no=' . $epoch_no;
        }

        $response = $this->getRequest('/epoch_block_protocols', $params, null, null, $horizontal_filter);

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
     * @param array horizontal_filter (optional)
     * @return Collection<NetworkTotals>
     */
    public function network_fetchTotals(string $epoch_no = null, array $horizontal_filter = null) {
        $params = null;
        if (!is_null($epoch_no)) {
            $params[] = '_epoch_no=' . $epoch_no;
        }

        $response = $this->getRequest('/totals', $params, null, null, $horizontal_filter);
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
     * @param array horizontal_filter (optional)
     * @return Collection<NetworkParamUpdates>
     */
    public function network_fetchParamUpdates(array $horizontal_filter = null) {
        $response = $this->getRequest('/param_updates', null, null, null, $horizontal_filter);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = NetworkParamUpdates::from($item);
        }
        return collect($returnArray);
    }

    // Pool ////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * A list of all currently registered/retiring (not retired) pools
     *
     * GET /pool_list
     *
     * @param array horizontal_filter (optional)
     * @return Collection<PoolList>
     */
    public function pool_fetchPoolList(array $horizontal_filter = null) {
        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $pools = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($pools > 0) {

            $response = $this->getRequest('/pool_list', null, $limit, $offset, $horizontal_filter);
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
     * @param array horizontal_filter (optional)
     * @return Collection<PoolStakeSnapshot>
     */
    public function pool_fetchPoolStakeSnapshot(string $pool_bech32, array $horizontal_filter = null) {
        $params[] = '_pool_bech32='.$pool_bech32;
        $response = $this->getRequest('/pool_stake_snapshot', $params, null, null, $horizontal_filter);
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
     * @param array horizontal_filter (optional)
     * @return Collection<PoolStakeSnapshot>
     */
    public function pool_fetchPoolDelegators(string $pool_bech32, array $horizontal_filter = null) {
        $params[] = '_pool_bech32='.$pool_bech32;

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $delegators = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($delegators > 0) {

            $response = $this->getRequest('/pool_delegators', $params, $limit, $offset, $horizontal_filter);
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
     * @param array horizontal_filter (optional)
     * @return Collection<PoolDelegatorHistory>
     */
    public function pool_fetchDelegatorsHistory(string $pool_bech32, string $epoch_no = null, array $horizontal_filter = null) {
        $params[] = '_pool_bech32='.$pool_bech32;

        if ($epoch_no) {
            $params[] = '_epoch_no='.$epoch_no;
        }

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $delegatorHistories = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($delegatorHistories > 0) {

            $response = $this->getRequest('/pool_delegators_history', $params, $limit, $offset, $horizontal_filter);
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
     * @param array horizontal_filter (optional)
     * @return Collection<PoolBlocks>
     */
    public function pool_fetchPoolBlocks(string $pool_bech32, string $epoch_no = null, array $horizontal_filter = null) {
        $params[] = '_pool_bech32='.$pool_bech32;

        if ($epoch_no) {
            $params[] = '_epoch_no='.$epoch_no;
        }

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $poolBlocks = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($poolBlocks > 0) {

            $response = $this->getRequest('/pool_blocks', $params, $limit, $offset, $horizontal_filter);
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
     * @param array horizontal_filter (optional)
     * @return Collection<PoolHistory>
     */
    public function pool_fetchPoolHistory(string $pool_bech32, string $epoch_no = null, array $horizontal_filter = null) {
        $params[] = '_pool_bech32='.$pool_bech32;

        if ($epoch_no) {
            $params[] = '_epoch_no='.$epoch_no;
        }

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $poolHistories = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($poolHistories > 0) {

            $response = $this->getRequest('/pool_history', $params, $limit, $offset, $horizontal_filter);
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
     * @param array horizontal_filter (optional))
     * @return Collection<PoolUpdates>
     */
    public function pool_fetchPoolUpdates(string $pool_bech32, array $horizontal_filter = null) {
        $params[] = '_pool_bech32='.$pool_bech32;

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $poolUpdates = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($poolUpdates > 0) {

            $response = $this->getRequest('/pool_updates', $params, $limit, $offset, $horizontal_filter);
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
     * @param array horizontal_filter (optional)
     * @return Collection<PoolRelays>
     */
    public function pool_fetchPoolRelays(array $horizontal_filter = null) {
        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $poolRelays = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($poolRelays > 0) {

            $response = $this->getRequest('/pool_relays', null, $limit, $offset, $horizontal_filter);
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
     *
     * @param array horizontal_filter (optional)
     * @return Collection<NativeScriptList>
     */
    public function script_fetchNativeScriptList(array $horizontal_filter = null) {
        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $nativeScripts = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($nativeScripts > 0) {

            $response = $this->getRequest('/native_script_list', null, $limit, $offset, $horizontal_filter);
            $nativeScriptArray = (array) json_decode($response);
            $nativeScripts = count($nativeScriptArray);

            foreach ($nativeScriptArray as $item) {
                $returnArray[] = NativeScriptList::from($item);
            }

            $offset = $offset + $nativeScripts;
        }
        return collect($returnArray);
    }

    /*
     * List of all existing Plutus script hashes along with their creation transaction hashes
     *
     * GET /plutus_script_list
     *
     * @param array horizontal_filter (optional)
     * @return Collection<PlutusScriptList>
     */
    public function script_fetchPlutusScriptList(array $horizontal_filter = null) {
        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $plutusScripts = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($plutusScripts > 0) {

            $response = $this->getRequest('/plutus_script_list', null, $limit, $offset, $horizontal_filter);
            $plutusScriptArray = (array) json_decode($response);
            $plutusScripts = count($plutusScriptArray);

            foreach ($plutusScriptArray as $item) {
                $returnArray[] = PlutusScriptList::from($item);
            }

            $offset = $offset + $plutusScripts;
        }
        return collect($returnArray);
    }

    /*
     * List of all redeemers for a given script hash
     *
     * GET /script_redeemers
     *
     * @param string script_hash
     * @param array horizontal_filter (optional)
     * @return Collection<ScriptRedeemer>
     */
    public function script_fetchScriptRedeemers(string $script_hash, array $horizontal_filter = null) {

        $params[] = '_script_hash='.$script_hash;

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $scriptRedeemers = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($scriptRedeemers > 0) {

            $response = $this->getRequest('/script_redeemers', $params, $limit, $offset, $horizontal_filter);
            $scriptRedeemerArray = (array) json_decode($response);
            $scriptRedeemers = count($scriptRedeemerArray);

            foreach ($scriptRedeemerArray as $item) {
                $returnArray[] = ScriptRedeemer::from($item);
            }

            $offset = $offset + $scriptRedeemers;
        }
        return collect($returnArray);
    }

    /*
     * List of datum information for given datum hashes
     *
     * POST /datum_info
     *
     * @param array datum_hashes
     */
    public function script_fetchDatumInfo(array $datum_hashes) {
        $postParams = [];
        $postParams['_datum_hashes'] = $datum_hashes;
        $response = $this->postRequest('/datum_info', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response) as $item) {
            $returnArray[] = DatumInformation::from($item);
        }
        return collect($returnArray);
    }

    // Stake Account ///////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Get a list of all stake addresses that have at least 1 transaction
     *
     * GET /account_list
     *
     * @param array horizontal_filter (optional)
     * @return Collection<AccountList>
     */
    public function account_fetchAccountList(array $horizontal_filter = null) {
        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $stakeAccounts = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($stakeAccounts > 0) {

            $response = $this->getRequest('/account_list', null, $limit, $offset, $horizontal_filter);
            $stakeAccountArray = (array) json_decode($response);
            $stakeAccounts = count($stakeAccountArray);

            foreach ($stakeAccountArray as $item) {
                $returnArray[] = AccountList::from($item);
            }

            $offset = $offset + $stakeAccounts;
        }
        return collect($returnArray);
    }

    /*
     * Get the account information for given stake addresses
     *
     * POST /account_info
     *
     * @param array stake_addresses
     * @return Collection<AccountInformation>
     */
    public function account_fetchAccountInfos(array $stake_addresses) {
        $postParams = [];
        $postParams['_stake_addresses'] = $stake_addresses;
        $response = $this->postRequest('/account_info', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response, true) as $item) {
            $returnArray[] = AccountInformation::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get a list of all UTxOs for a given stake address (account)
     *
     * GET /account_utxos
     *
     * @param string stake_address
     * @param array horizontal_filter (optional)
     * @return Collection<AccountTransactions>
     */
    public function account_fetchAccountUtxos(string $stake_address, array $horizontal_filter = null) {

        $params[] = '_stake_address='.$stake_address;

        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $accountTxs = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($accountTxs > 0) {

            $response = $this->getRequest('/account_utxos', $params, $limit, $offset, $horizontal_filter);
            $accountTxArray = (array) json_decode($response);
            $accountTxs = count($accountTxArray);

            foreach ($accountTxArray as $item) {
                $returnArray[] = AccountTransactions::from($item);
            }

            $offset = $offset + $accountTxs;
        }
        return collect($returnArray);
    }

    /*
     * Get the cached account information for given stake addresses, effective for registered accounts
     *
     * POST /account_info_cached
     *
     * @param array stake_addresses
     * @return Collection<AccountInformation>
     */
    public function account_fetchAccountInfoCached(array $stake_addresses) {
        $postParams = [];
        $postParams['_stake_addresses'] = $stake_addresses;
        $response = $this->postRequest('/account_info_cached', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response, true) as $item) {
            $returnArray[] = AccountInformation::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get the full rewards history (including MIR) for given stake addresses
     *
     * POST /account_rewards
     *
     * @param array stake_addresses
     * @return Collection<AccountInformation>
     */
    public function account_fetchAccountRewards(array $stake_addresses) {
        $postParams = [];
        $postParams['_stake_addresses'] = $stake_addresses;
        $response = $this->postRequest('/account_rewards', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response, true) as $item) {
            $returnArray[] = AccountRewards::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get the account updates (registration, deregistration, delegation and withdrawals) for given stake addresses
     *
     * POST /account_updates
     *
     * @param array stake_addresses
     * @return Collection<AccountUpdates>
     */
    public function account_fetchAccountUpdates(array $stake_addresses) {
        $postParams = [];
        $postParams['_stake_addresses'] = $stake_addresses;
        $response = $this->postRequest('/account_updates', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response, true) as $item) {
            $returnArray[] = AccountUpdates::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get all addresses associated with given staking accounts
     *
     * POST /account_addresses
     *
     * @param array stake_addresses
     * @return Collection<AccountAddresses>
     */
    public function account_fetchAccountAddresses(array $stake_addresses) {
        $postParams = [];
        $postParams['_stake_addresses'] = $stake_addresses;
        $response = $this->postRequest('/account_addresses', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response, true) as $item) {
            $returnArray[] = AccountAddresses::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get the native asset balance for a given stake address
     *
     * POST /account_assets
     *
     * @param array stake_addresses
     * @return Collection<AccountAssets>
     */
    public function account_fetchAccountAssets(array $stake_addresses) {
        $postParams = [];
        $postParams['_stake_addresses'] = $stake_addresses;
        $response = $this->postRequest('/account_assets', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response, true) as $item) {
            $returnArray[] = AccountAssets::from($item);
        }
        return collect($returnArray);
    }

    /*
     * Get the staking history of given stake addresses (accounts)
     *
     * POST /account_history
     *
     * @param array stake_addresses
     * @return Collection<AccountHistory>
     */
    public function account_fetchAccountHistory(array $stake_addresses) {
        $postParams = [];
        $postParams['_stake_addresses'] = $stake_addresses;
        $response = $this->postRequest('/account_history', $postParams);
        $returnArray = [];
        foreach ((array) json_decode($response, true) as $item) {
            $returnArray[] = AccountHistory::from($item);
        }
        return collect($returnArray);
    }


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
     * @param array horizontal_filter (optional)
     * @return Collection<TransactionMetadataLabels>
     */
    public function transaction_fetchTransactionMetadataLabels(array $horizontal_filter = null) {
        $limit = self::KOIOS_API_LIMIT;
        $offset = self::KOIOS_OFFSET_START;
        $transactionMetadata = self::KOIOS_COUNT_START;
        $returnArray = [];

        while($transactionMetadata > 0) {

            $response = $this->getRequest('/tx_metalabels', null, $limit, $offset, $horizontal_filter);
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
     * Assuming ${data} is a raw binary serialized transaction on the file-system.
     * If using a CLI-generated tx file, please ensure to deserialise (using `xxd -p -r <<< $(jq .cborHex ${tx.signed}) > ${data}`) first before submitting.
     *
     * POST /submittx
     *
     * @param
     */
    public function transaction_submitTransaction(StreamInterface $data) {
        Http::retry(5, 100)->timeout(5)->withBody($data, 'application/cbor')->post($this->baseUrl.'/submittx');
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
