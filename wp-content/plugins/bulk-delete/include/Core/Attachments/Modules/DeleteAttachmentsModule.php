<?php

namespace BulkWP\BulkDelete\Core\Attachments\Modules;

use BulkWP\BulkDelete\Core\Attachments\AttachmentsModule;

defined('ABSPATH') || exit; // Exit if accessed directly.

/**
 * Delete Attachments by Name.
 *
 * @since 6.0.0
 */
class DeleteAttachmentsModule extends AttachmentsModule
{
    protected function initialize()
    {
        $this->item_type     = 'attachments';
        $this->field_slug    = 'attachments_by_name';
        $this->meta_box_slug = 'bd_delete_attachments_by_name';
        $this->action        = 'delete_attachments_by_name';
        $this->messages      = array(
            'box_label' => __('Delete Attachments', 'bulk-delete'),
            'scheduled' => __('The selected attachments are scheduled for deletion', 'bulk-delete'),
        );
    }

    public function render()
    {
?>
        <fieldset class="options">

            <h4>Choose your attachment settings <span class="open-upsell pro-feature-inline">Available in PRO</span></h4>
            <table class="optiontable">

                <tbody>
                    <tr>
                        <td colspan="2">
                            <input name="smbd_at_attachment" value="attached" type="radio" checked="checked" disabled> Delete only attached attachments
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <input name="smbd_at_attachment" value="unattached" type="radio" checked="checked" disabled> Delete only unattached attachments
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <h4>Choose attachment mime type</h4>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <select name="smbd_at_mime_type" class="select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" disabled>
                                <option value="all">All mime types</option>
                                <option value="application">All application</option>
                                <option value="application/java">application/java</option>
                                <option value="application/javascript">application/javascript</option>
                                <option value="application/msword">application/msword</option>
                                <option value="application/octet-stream">application/octet-stream</option>
                                <option value="application/onenote">application/onenote</option>
                                <option value="application/oxps">application/oxps</option>
                                <option value="application/pdf">application/pdf</option>
                                <option value="application/rar">application/rar</option>
                                <option value="application/rtf">application/rtf</option>
                                <option value="application/ttaf+xml">application/ttaf+xml</option>
                                <option value="application/vnd.apple.keynote">application/vnd.apple.keynote</option>
                                <option value="application/vnd.apple.numbers">application/vnd.apple.numbers</option>
                                <option value="application/vnd.apple.pages">application/vnd.apple.pages</option>
                                <option value="application/vnd.ms-access">application/vnd.ms-access</option>
                                <option value="application/vnd.ms-excel">application/vnd.ms-excel</option>
                                <option value="application/vnd.ms-excel.addin.macroEnabled.12">application/vnd.ms-excel.addin.macroEnabled.12</option>
                                <option value="application/vnd.ms-excel.sheet.binary.macroEnabled.12">application/vnd.ms-excel.sheet.binary.macroEnabled.12</option>
                                <option value="application/vnd.ms-excel.sheet.macroEnabled.12">application/vnd.ms-excel.sheet.macroEnabled.12</option>
                                <option value="application/vnd.ms-excel.template.macroEnabled.12">application/vnd.ms-excel.template.macroEnabled.12</option>
                                <option value="application/vnd.ms-powerpoint">application/vnd.ms-powerpoint</option>
                                <option value="application/vnd.ms-powerpoint.addin.macroEnabled.12">application/vnd.ms-powerpoint.addin.macroEnabled.12</option>
                                <option value="application/vnd.ms-powerpoint.presentation.macroEnabled.12">application/vnd.ms-powerpoint.presentation.macroEnabled.12</option>
                                <option value="application/vnd.ms-powerpoint.slide.macroEnabled.12">application/vnd.ms-powerpoint.slide.macroEnabled.12</option>
                                <option value="application/vnd.ms-powerpoint.slideshow.macroEnabled.12">application/vnd.ms-powerpoint.slideshow.macroEnabled.12</option>
                                <option value="application/vnd.ms-powerpoint.template.macroEnabled.12">application/vnd.ms-powerpoint.template.macroEnabled.12</option>
                                <option value="application/vnd.ms-project">application/vnd.ms-project</option>
                                <option value="application/vnd.ms-word.document.macroEnabled.12">application/vnd.ms-word.document.macroEnabled.12</option>
                                <option value="application/vnd.ms-word.template.macroEnabled.12">application/vnd.ms-word.template.macroEnabled.12</option>
                                <option value="application/vnd.ms-write">application/vnd.ms-write</option>
                                <option value="application/vnd.ms-xpsdocument">application/vnd.ms-xpsdocument</option>
                                <option value="application/vnd.oasis.opendocument.chart">application/vnd.oasis.opendocument.chart</option>
                                <option value="application/vnd.oasis.opendocument.database">application/vnd.oasis.opendocument.database</option>
                                <option value="application/vnd.oasis.opendocument.formula">application/vnd.oasis.opendocument.formula</option>
                                <option value="application/vnd.oasis.opendocument.graphics">application/vnd.oasis.opendocument.graphics</option>
                                <option value="application/vnd.oasis.opendocument.presentation">application/vnd.oasis.opendocument.presentation</option>
                                <option value="application/vnd.oasis.opendocument.spreadsheet">application/vnd.oasis.opendocument.spreadsheet</option>
                                <option value="application/vnd.oasis.opendocument.text">application/vnd.oasis.opendocument.text</option>
                                <option value="application/vnd.openxmlformats-officedocument.presentationml.presentation">application/vnd.openxmlformats-officedocument.presentationml.presentation</option>
                                <option value="application/vnd.openxmlformats-officedocument.presentationml.slide">application/vnd.openxmlformats-officedocument.presentationml.slide</option>
                                <option value="application/vnd.openxmlformats-officedocument.presentationml.slideshow">application/vnd.openxmlformats-officedocument.presentationml.slideshow</option>
                                <option value="application/vnd.openxmlformats-officedocument.presentationml.template">application/vnd.openxmlformats-officedocument.presentationml.template</option>
                                <option value="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">application/vnd.openxmlformats-officedocument.spreadsheetml.sheet</option>
                                <option value="application/vnd.openxmlformats-officedocument.spreadsheetml.template">application/vnd.openxmlformats-officedocument.spreadsheetml.template</option>
                                <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document">application/vnd.openxmlformats-officedocument.wordprocessingml.document</option>
                                <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.template">application/vnd.openxmlformats-officedocument.wordprocessingml.template</option>
                                <option value="application/wordperfect">application/wordperfect</option>
                                <option value="application/x-7z-compressed">application/x-7z-compressed</option>
                                <option value="application/x-gzip">application/x-gzip</option>
                                <option value="application/x-tar">application/x-tar</option>
                                <option value="application/zip">application/zip</option>
                                <option value="audio">All audio</option>
                                <option value="audio/aac">audio/aac</option>
                                <option value="audio/flac">audio/flac</option>
                                <option value="audio/midi">audio/midi</option>
                                <option value="audio/mpeg">audio/mpeg</option>
                                <option value="audio/ogg">audio/ogg</option>
                                <option value="audio/wav">audio/wav</option>
                                <option value="audio/x-matroska">audio/x-matroska</option>
                                <option value="audio/x-ms-wax">audio/x-ms-wax</option>
                                <option value="audio/x-ms-wma">audio/x-ms-wma</option>
                                <option value="audio/x-realaudio">audio/x-realaudio</option>
                                <option value="image">All image</option>
                                <option value="image/avif">image/avif</option>
                                <option value="image/bmp">image/bmp</option>
                                <option value="image/gif">image/gif</option>
                                <option value="image/heic">image/heic</option>
                                <option value="image/heic-sequence">image/heic-sequence</option>
                                <option value="image/heif">image/heif</option>
                                <option value="image/heif-sequence">image/heif-sequence</option>
                                <option value="image/jpeg">image/jpeg</option>
                                <option value="image/png">image/png</option>
                                <option value="image/tiff">image/tiff</option>
                                <option value="image/webp">image/webp</option>
                                <option value="image/x-icon">image/x-icon</option>
                                <option value="text">All text</option>
                                <option value="text/calendar">text/calendar</option>
                                <option value="text/css">text/css</option>
                                <option value="text/csv">text/csv</option>
                                <option value="text/html">text/html</option>
                                <option value="text/plain">text/plain</option>
                                <option value="text/richtext">text/richtext</option>
                                <option value="text/tab-separated-values">text/tab-separated-values</option>
                                <option value="text/vtt">text/vtt</option>
                                <option value="video">All video</option>
                                <option value="video/3gpp">video/3gpp</option>
                                <option value="video/3gpp2">video/3gpp2</option>
                                <option value="video/avi">video/avi</option>
                                <option value="video/divx">video/divx</option>
                                <option value="video/mp4">video/mp4</option>
                                <option value="video/mpeg">video/mpeg</option>
                                <option value="video/ogg">video/ogg</option>
                                <option value="video/quicktime">video/quicktime</option>
                                <option value="video/webm">video/webm</option>
                                <option value="video/x-flv">video/x-flv</option>
                                <option value="video/x-matroska">video/x-matroska</option>
                                <option value="video/x-ms-asf">video/x-ms-asf</option>
                                <option value="video/x-ms-wm">video/x-ms-wm</option>
                                <option value="video/x-ms-wmv">video/x-ms-wmv</option>
                                <option value="video/x-ms-wmx">video/x-ms-wmx</option>
                            </select><span class="select2 select2-container select2-container--default" dir="ltr" style="width: 513px;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-smbd_at_mime_type-vh-container"><span class="select2-selection__rendered" id="select2-smbd_at_mime_type-vh-container" title="All mime types">All mime types</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="optiontable">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <h4>Choose your filtering options</h4>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">
                            <input name="smbd_attachments_restrict" id="smbd_attachments_restrict" value="true" type="checkbox" disabled>
                        </td>
                        <td>
                            Only restrict to attachments which are <select name="smbd_attachments_op" id="smbd_attachments_op" disabled>
                                <option value="before">older than</option>
                                <option value="after">posted within last</option>
                            </select>
                            <input type="number" name="smbd_attachments_days" id="smbd_attachments_days" class="screen-per-page" disabled="" value="0" min="0">days
                        </td>
                    </tr>
                    <tr>
                        <td scope="row" colspan="2">
                            <input name="smbd_attachments_force_delete" value="false" type="radio" checked="" disabled> Move to Trash <input name="smbd_attachments_force_delete" value="true" type="radio" disabled> Delete permanently
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">
                            <input name="smbd_attachments_limit" id="smbd_attachments_limit" value="true" type="checkbox" disabled>
                        </td>
                        <td>
                            Only delete first <input type="number" name="smbd_attachments_limit_to" id="smbd_attachments_limit_to" class="screen-per-page" disabled="" value="0" min="0"> attachments.
                            Use this option if there are more than 1000 attachments and the script times out. </td>
                    </tr>
                    <tr>
                        <td scope="row" colspan="2">
                            <input name="smbd_attachments_cron" value="false" type="radio" checked="checked" disabled> Delete now <input name="smbd_attachments_cron" value="true" type="radio" id="smbd_attachments_cron" disabled> Schedule <input name="smbd_attachments_cron_start" id="smbd_attachments_cron_start" value="now" type="text" class="hasDatepicker" disabled>repeat <select name="smbd_attachments_cron_freq" id="smbd_attachments_cron_freq" disabled>
                                <option value="-1">Don't repeat</option>
                                <option value="hourly">Once Hourly</option>
                                <option value="twicedaily">Twice Daily</option>
                                <option value="daily">Once Daily</option>
                                <option value="weekly">Once Weekly</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td scope="row" colspan="2">
                            Enter time in <strong>Y-m-d H:i:s</strong> format or enter <strong>now</strong> to use current time 
                        <span class="open-upsell pro-feature-inline">Available in PRO</span>
                    </td> 

                    </tr>
                </tbody>
            </table>
        </fieldset>
<?php
    }

    protected function append_to_js_array($js_array)
    {
        $js_array['validators'][$this->action] = 'validateTermName';
        $js_array['error_msg'][$this->action]  = 'enterTermName';
        $js_array['msg']['enterTermName']        = __('Please enter the term name that should be deleted', 'bulk-delete');

        $js_array['pre_action_msg'][$this->action] = 'deleteTermsWarning';
        $js_array['msg']['deleteTermsWarning']       = __('Are you sure you want to delete all the terms based on the selected option?', 'bulk-delete');

        return $js_array;
    }

    protected function convert_user_input_to_options($request, $options)
    {
        $options['operator'] = sanitize_text_field(bd_array_get($request, 'smbd_' . $this->field_slug . '_operator'));
        $options['value']    = sanitize_text_field(bd_array_get($request, 'smbd_' . $this->field_slug . '_value'));

        return $options;
    }

    protected function get_term_ids_to_delete($options)
    {
        $term_ids = array();
        $value    = $options['value'];
        $operator = $options['operator'];
        if (empty($value)) {
            return $term_ids;
        }

        switch ($operator) {
            case 'equal_to':
                $term_ids = $this->get_terms_that_are_equal_to($value, $options);
                break;

            case 'not_equal_to':
                $term_ids = $this->get_terms_that_are_not_equal_to($value, $options);
                break;

            case 'starts_with':
                $term_ids = $this->get_terms_that_starts_with($value, $options);
                break;

            case 'ends_with':
                $term_ids = $this->get_terms_that_ends_with($value, $options);
                break;

            case 'contains':
                $term_ids = $this->get_terms_that_contains($value, $options);
                break;

            case 'not_contains':
                $term_ids = $this->get_terms_that_not_contains($value, $options);
                break;
        }

        return $term_ids;
    }

    /**
     * Get terms with name that are equal to a specific string.
     *
     * @param string $value   Value to compare.
     * @param array  $options User options.
     *
     * @return int[] Term ids.
     */
    protected function get_terms_that_are_equal_to($value, $options)
    {
        $query = array(
            'taxonomy' => $options['taxonomy'],
            'name'     => $value,
        );

        return $this->query_terms($query);
    }

    /**
     * Get terms with that name that is not equal to a specific string.
     *
     * @param string $value   Value to compare.
     * @param array  $options User options.
     *
     * @return int[] Term ids.
     */
    protected function get_terms_that_are_not_equal_to($value, $options)
    {
        $name_like_args = array(
            'name'     => $value,
            'taxonomy' => $options['taxonomy'],
        );

        $query = array(
            'taxonomy' => $options['taxonomy'],
            'exclude'  => $this->query_terms($name_like_args),
        );

        return $this->query_terms($query);
    }

    /**
     * Get terms with name that start with a specific string.
     *
     * @param string $starts_with Substring to search.
     * @param array  $options     User options.
     *
     * @return int[] Term ids.
     */
    protected function get_terms_that_starts_with($starts_with, $options)
    {
        $term_ids = array();
        $terms    = $this->get_all_terms($options['taxonomy']);

        foreach ($terms as $term) {
            if (bd_starts_with($term->name, $starts_with)) {
                $term_ids[] = $term->term_id;
            }
        }

        return $term_ids;
    }

    /**
     * Get terms with name that ends with a specific string.
     *
     * @param string $ends_with Substring to search.
     * @param array  $options   User options.
     *
     * @return int[] Term ids.
     */
    protected function get_terms_that_ends_with($ends_with, $options)
    {
        $term_ids = array();
        $terms    = $this->get_all_terms($options['taxonomy']);

        foreach ($terms as $term) {
            if (bd_ends_with($term->name, $ends_with)) {
                $term_ids[] = $term->term_id;
            }
        }

        return $term_ids;
    }

    /**
     * Get terms with name that contains a specific string.
     *
     * @param string $contains Substring to search.
     * @param array  $options  User options.
     *
     * @return int[] Term ids.
     */
    protected function get_terms_that_contains($contains, $options)
    {
        $term_ids = array();
        $terms    = $this->get_all_terms($options['taxonomy']);

        foreach ($terms as $term) {
            if (bd_contains($term->name, $contains)) {
                $term_ids[] = $term->term_id;
            }
        }

        return $term_ids;
    }

    /**
     * Get terms with name that doesn't contain a specific string.
     *
     * @param string $contains Substring to search.
     * @param array  $options  User options.
     *
     * @return int[] Term ids.
     */
    protected function get_terms_that_not_contains($contains, $options)
    {
        $term_ids = array();
        $terms    = $this->get_all_terms($options['taxonomy']);

        foreach ($terms as $term) {
            if (! bd_contains($term->name, $contains)) {
                $term_ids[] = $term->term_id;
            }
        }

        return $term_ids;
    }
}
