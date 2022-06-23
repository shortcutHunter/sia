(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetNumber', widgetNumber);

    widgetNumber.$inject = ['$compile', 'dataservice', '$state'];

    /* @ngInject */
    function widgetNumber(compile, dataservice, state) {
        var directive = {
            restrict: 'A',
            link: link,
            scope: {},
            template: "<span ng-bind='wnVal'></span>"
        };
        return directive;

        function link(scope, element, attrs)
        {
            scope.$watch(() => attrs.widgetNumber, (newVal, oldVal) => {
                if (newVal) {
                    let editedVal = newVal;
                    editedVal = parseInt(editedVal).toFixed(2).replace('.', ',').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    scope.wnVal = editedVal;
                } else {
                    scope.wnVal = 0;
                }
            });
        }
    }

})();