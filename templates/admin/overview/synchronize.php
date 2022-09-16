<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'error' );
}
global $current_user; ?>
<div class="wrap">
    <h1 class="wp-heading-inline">Cruise Factory Import</h1>

    <div class="tablenav bottom">
        <div class="alignright actions bulkactions">
            <button id="atd-cfi-increment-import" class="button action" role="button">Synchronize Now</button>
        </div>
    </div>

    <table id="atd-cfi-services" class="widefat striped table-view-list">
        <thead>
        <tr>
            <th class="row-title">Service</th>
            <th>Last Updated</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="2" style="text-align: center;">
                <div class="spinner is-active"
                     style="float:none;width:auto;height:auto;padding:10px 0 10px 50px;background-position:20px 0;"></div>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <th class="row-title">Service</th>
            <th>Last Updated</th>
        </tr>
        </tfoot>
    </table>
</div>