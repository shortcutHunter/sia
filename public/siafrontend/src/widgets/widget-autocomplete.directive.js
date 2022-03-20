(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetAutoComplete', widgetAutoComplete);

  /* @ngInject */
  function widgetAutoComplete() {
    var directive = {
        restrict: 'E',
        link    : link,
        require : 'ngModel',
        scope: {
            data: '='
        },
        template: `
            <div class="easy-complete-container">
                <input type="text" class="form-control form-control-sm">
            </div>
        `,
        replace: true
    };
    return directive;

    function link(scope, element, attr, ctrl) {

        scope.attr = attr;

        var options = {
            url: function(phrase) {
                if (attr.normalSearch) {
                    return `/${attr.table}/get?nama=${phrase}`;
                }else{
                    if (attr.table == 'mahasiswa') {
                        let data = scope.$parent.$parent.vm.data;
                        return `/search/${attr.table}?nama=${phrase}&semester=${data.semester_id}`;
                    }

                    if (attr.table == 'karyawan') {
                        let data = scope.$parent.$parent.vm.data;
                        return `/search/${attr.table}?nama=${phrase}&jenis_karyawan=${attr.jenis}`;
                    }

                    return `/search/${attr.table}?nama=${phrase}`;
                }
            },
            listLocation: "data",
            list: {
                maxNumberOfElements: 10,
                onSelectItemEvent: function()
                {
                    var data = $(element).find('input').getSelectedItemData();
                    if(!scope.$parent.selectedData){
                        scope.$parent.selectedData = {};
                    }
                    scope.$parent.selectedData[attr.ngModel] = data;
                    ctrl.$setViewValue(data.id);
                }
            }
        };

        if (attr.normalSearch) {
            options['getValue'] = function(value){
                return value.nama;
            };
        }else{
            options['getValue'] = function(value){
                return value.orang.nama;
            };
        }

        $(element).find('input').easyAutocomplete(options);

        scope.$watch(() => ctrl.$modelValue, function(newVal, oldVal){
            if (newVal == false) {
                $(element).find('input').val('');
            }
        });

        scope.$watch(() => scope.data, function(newVal, oldVal){
            if (newVal) {
                $(element).find('input').val(newVal.nama);
            }
        });
    }
  }
})();
