(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetInputNumber', widgetInputNumber);

    widgetInputNumber.$inject = ['$compile', 'dataservice', '$state'];

    /* @ngInject */
    function widgetInputNumber(compile, dataservice, state) {
        var directive = {
            restrict: 'A',
            require: 'ngModel',
            link: link,scope: {
                ngModel: '='
            },
            template: `
                <input type="text" ng-model="inputVal" class="form-control form-control-sm" {{ required ? 'required' : '' }}>
            `
        };
        return directive;

        function link(scope, element, attrs, ngModelCtrl)
        {
            let firstTime = true;
            scope.required = attrs['required'];

            element.find('input').inputFilter(function(value) {
                if (!value) {
                    return true;
                }
                return /^[0-9.,]+$/.test(value);
            });

            element.find('input').on('input keydown keyup mousedown mouseup select contextmenu drop focusout', function(event) {
                $(this).val(function(index, value) {
                    let endResult = value
                      // Keep only digits, decimal points, and dashes at the start of the string:
                      .replace(/[^\d,-]|(?!^)-/g, "")
                      // Remove duplicated decimal points, if they exist:
                      .replace(/^([^,]*\,)(.*$)/, (_, g1, g2) => g1 + g2.replace(/\,/g, ''))
                      // Keep only two digits past the decimal point:
                      .replace(/\,(\d{2})\d+/, '.$1')
                      .replace(".", ",")
                      // Add thousands separators:
                      .replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                    firstTime = false;
                    updateModel(endResult.replace(/[^\d,-]|(?!^)-/g, "").replace(",", "."));

                    return endResult;
                });
            });

            scope.$watch(() => ngModelCtrl.$modelValue, (newVal, oldVal) => {
                if (newVal && firstTime) {
                    element.find('input').val(newVal).trigger('input');
                }
            });

            function updateView(value) {
                ngModelCtrl.$viewValue = value;
                ngModelCtrl.$render(); 
            }

            function updateModel(value) {
                ngModelCtrl.$modelValue = value;
                scope.ngModel = value; // overwrites ngModel value
            }
        }
    }

})();