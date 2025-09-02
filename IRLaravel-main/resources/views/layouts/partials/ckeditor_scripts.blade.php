<script>
    $(document).ready(function () {
        /**
         * Integrate Youtube plugin to CKEditor
         * @link https://github.com/fonini/ckeditor-youtube-plugin
         */
        CKEDITOR.plugins.addExternal('youtube', '../ckeditor-youtube-plugin/youtube/');

        $('.ckeditor').each(function () {
            var self = this;
            var editor = $(self);
            // Default config
            var config = {
                /**
                 * Integrate Youtube plugin to CKEditor
                 * @link https://github.com/fonini/ckeditor-youtube-plugin
                 */
                extraPlugins: 'youtube',
                /**
                 * WYSIWYGE - Cannot upload image - Incorrect server response
                 * @link https://www.question2answer.org/qa/63155/wysiwyge-cannot-upload-image-incorrect-server-response
                 * @type {string}
                 */
                filebrowserUploadMethod: 'form',
                /**
                 * Integrate KCFinder with CKEditor
                 * @link https://kcfinder.sunhater.com/integrate
                 */
                filebrowserBrowseUrl: "{!! URL::asset('/assets/kcfinder/browse.php?opener=ckeditor&type=files') !!}",
                filebrowserImageBrowseUrl: "{!! URL::asset('/assets/kcfinder/browse.php?opener=ckeditor&type=images') !!}",
                filebrowserFlashBrowseUrl: "{!! URL::asset('/assets/kcfinder/browse.php?opener=ckeditor&type=flash') !!}",
                filebrowserUploadUrl: "{!! URL::asset('/assets/kcfinder/upload.php?opener=ckeditor&type=files') !!}",
                filebrowserImageUploadUrl: "{!! URL::asset('/assets/kcfinder/upload.php?opener=ckeditor&type=images') !!}",
                filebrowserFlashUploadUrl: "{!! URL::asset('/assets/kcfinder/upload.php?opener=ckeditor&type=flash') !!}",
            };

            /**
             * Full toolbar
             * @link https://ckeditor.com/latest/samples/old/toolbar/toolbar.html
             */
            // Toolbar configuration generated automatically by the editor based on config.toolbarGroups.
            config.toolbar = [
                {
                    name: 'document',
                    groups: ['mode', 'document', 'doctools'],
                    items: ['Source']
                },
                {
                    name: 'clipboard',
                    groups: ['clipboard', 'undo'],
                    items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
                },
                {
                    name: 'editing',
                    groups: ['find', 'selection', 'spellchecker'],
                    items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt']
                },
                {
                    name: 'insert',
                    items: ['Image', 'Youtube', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar']
                },
                '/',
                {
                    name: 'basicstyles',
                    groups: ['basicstyles', 'cleanup'],
                    items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
                    items: ['NumberedList', 'BulvaredList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'/*, 'Language'*/]
                },
                {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
                '/',
                {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
                {name: 'colors', items: ['TextColor', 'BGColor']},
                {name: 'tools', items: ['Maximize', 'ShowBlocks']},
                {name: 'others', items: ['-']},
                {name: 'about', items: ['About']}
            ];

            // Custom config
            // Height
            if (typeof editor.data('height') !== 'undefined') {
                config['height'] = editor.data('height');
            }

            // Set to CKEditor
            if (typeof $(self).attr('name') !== 'undefined') {
                CKEDITOR.replace($(self).attr('name'), config);
            } else {
                $(self).ckeditor(config);
            }
        });
    })
</script>