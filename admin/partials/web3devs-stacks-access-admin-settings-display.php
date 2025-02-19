<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://stacksaccess.com
 * @since      1.0.0
 *
 * @package    Web3devs_Stacks_Access
 * @subpackage Web3devs_Stacks_Access/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2>Stacks Access Settings</h2>
    <table class="wp-list-table widefat fixed striped table-view-list pages">
        <thead>
            <tr>
                <th scope="col" id="contract" class="manage-column column-primary">
                    Contract address
                </th>
                <th scope="col" id="symbol" class="manage-column">
                    Label
                </th>
                <th scope="col" id="actions" class="manage-column">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (!is_array($coins) || empty($coins)) : ?>
                <tr>
                    <td colspan="3">
                        <p>No tokens found.</p>
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($coins as $key => $coin): ?>
                    <tr class="iedit author-self level-0 type-page status-draft hentry">
                        <td class="has-row-actions column-primary"><?php echo esc_html($coin['contract']); ?></td>
                        <td class="has-row-actions"><?php echo esc_html($coin['symbol']); ?></td>
                        <td class="has-row-actions">
                            <form method="POST" action="options.php">
                                <?php
                                    $option_name = 'web3devs_stacks_access_configured_coins_setting';
                                    settings_fields('web3devs_stacks_access_general_settings');
                                ?>
                                <input id="<?php echo esc_attr($option_name.'_remove_contract'); ?>" type="hidden" name="<?php echo esc_attr($option_name.'[remove][contract]'); ?>" value="<?php echo esc_attr($coin['contract']); ?>">
                                <?php submit_button('Remove'); ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ;?>
            <?php endif ;?>
        </tbody>
        <tfoot>
            <tr>
                <th scope="col" class="manage-column column-primary">Contract address</th>
                <th scope="col" class="manage-column">Symbol</th>
                <th scope="col" class="manage-column">Actions</th>
            </tr>
        </tfoot>
    </table>
    <?php settings_errors(); ?>
    <form method="POST" action="options.php">
        <?php
            settings_fields( 'web3devs_stacks_access_general_settings' );
            do_settings_sections( 'web3devs_stacks_access_general_settings' );
        ?>
        <strong>NOTES!</strong>
        <ol>
            <li>
                Use <strong>stx</strong> as the "Contract address" for Stacks native token (STX).
            </li>
            <li>
                "Contract address" for any other token should be in the format used by Stacks API, ex. <strong>ST24YYAWQ4DK4RKCKK1RP4PX0X5SCSXTWQXFGVCVY.far-amber-bee::bullets</strong><br />
                In order to obtain the address:
                <ol>
                    <li>Go to <a href="https://explorer.stacks.co/" target="_blank">Stacks Explorer</a> and find your token of choice. Ex. <a href="https://explorer.stacks.co/txid/ST24YYAWQ4DK4RKCKK1RP4PX0X5SCSXTWQXFGVCVY.far-amber-bee?chain=testnet" target="_blank">Bullets</a></li>
                    <li>Take a note of the <strong>Contracts name</strong></li>
                    <li>Note that the <strong>::bullets</strong> value is nowehere to be seen - don't panic!</li>
                    <li>Scroll down to the contract's <strong>Source code</strong></li>
                    <li>Find the <strong>define-non-fungible-token bullets uint</strong> - bullets is the  token's name, uint is just a variable type</li>
                    <li>Now you have everything to compose the contract address</li>
                    <li>Compose the final contract address from contract name and token name</li>
                </ol>
            </li>
        </ol>
        <?php submit_button('Add Token'); ?>
    </form>

    <form method="POST" action="options.php">
        <?php
            settings_fields( 'web3devs_stacks_access_denial_page_settings' );
            do_settings_sections( 'web3devs_stacks_access_denial_page_settings' );
        ?>
        Users without access to the token will be redirected to this page.
        <?php submit_button('Save denial page'); ?>
    </form>
</div>