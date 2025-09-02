function CustomSelect2() {
}

CustomSelect2.fn = {
    init: function () {
        CustomSelect2.fn.action.call(this);
        CustomSelect2.fn.removeValueInArray.call(this);
        CustomSelect2.fn.addInputList.call(this);
    },

    addInputList: function () {
        $(document).on("change", "#selectFeaturedProducts.select2-tags", function (e) {
            $('input[name=listProduct]').val($(this).val());
        });
    },

    removeValueInArray: function () {
        Array.prototype.remove = function () {
            var what, a = arguments, L = a.length, ax;
            while (L && this.length) {
                what = a[--L];
                while ((ax = this.indexOf(what)) !== -1) {
                    this.splice(ax, 1);
                }
            }
            return this;
        };
    },

    action: function () {
        var id = "selectFeaturedProducts";
        var objSelector = $('#' + id);
        var jsonData = objSelector.data('json');
        var jsonDataDefault = objSelector.data('default');

        objSelector.select2({
            placeholder: Lang.get('category.select_products'),
            allowClear: true,
            width: '100%',
            data: typeof MainManager.fn.jsonData != 'undefined' ? MainManager.fn.jsonData : jsonData,
        });

        if (jsonDataDefault) {
            objSelector.val(jsonDataDefault).trigger('change.select2');
        }

        objSelector.on('select2:open', function (e) {
            var selectedCategories = $('#category_ids').val();

            setTimeout(function () {
                $('#select2-' + id + '-results li[role=group]').each(function (k, v) {
                    var category = $(v);
                    var countItem = category.find('li[role=treeitem]').length;
                    if (countItem > 0 && countItem === category.find('li[role=treeitem][aria-selected=true]').length) {
                        category.find('.select2-results__group').addClass('all');
                    }

                    // if ($('#limit_products').length && $('#limit_products').is(':checked')) {
                    //     for (var i = 0; i < jsonData.length; i++) {
                    //         if (category.find('.select2-results__group').text() === jsonData[i].text.toString()) {
                    //             if (
                    //                 (typeof jsonData[i] != 'undefined')
                    //                 && (typeof jsonData[i].id != 'undefined')
                    //                 && ($.inArray(jsonData[i].id.toString(), selectedCategories) > -1)
                    //             ) {
                    //                 category.find('.select2-results__group').attr('aria-disabled', true);
                    //             }
                    //         }
                    //     }
                    // }
                });
            }, 1);

            $('#select2-' + id + '-results').on('click', function (event) {
                event.stopPropagation();

                var data = $(event.target).html();
                var selectedOptionGroup = data.toString().trim();

                var groupchildren = [];
                var options = [];

                for (var i = 0; i < jsonData.length; i++) {
                    if (selectedOptionGroup.toString() === jsonData[i].text.toString()) {
                        // Stop if is disabled
                        if (typeof jsonData[i].disabled != 'undefined') {
                            return false;
                        }

                        for (var j = 0; j < jsonData[i].children.length; j++) {
                            groupchildren.push(jsonData[i].children[j].id);
                        }
                    }
                }

                options = objSelector.val();
                if (options === null || options === '') {
                    options = [];
                }

                for (var i = 0; i < groupchildren.length; i++) {
                    var count = 0;
                    for (var j = 0; j < options.length; j++) {
                        if (options[j].toString() === groupchildren[i].toString()) {
                            count++;
                            break;
                        }
                    }
                    if (count === 0) {
                        options.push(groupchildren[i].toString());
                    }
                }

                if ($(event.target).hasClass('all')) {
                    for (var i = 0; i < groupchildren.length; i++) {
                        options.remove(groupchildren[i]);
                    }
                }

                objSelector.val(options);
                objSelector.trigger('change');
                objSelector.select2('close');
            });
        });
    },

    rule: function () {
        $(document).ready(function () {
            CustomSelect2.fn.init.call(this);
        });
    },
};

CustomSelect2.fn.rule();
