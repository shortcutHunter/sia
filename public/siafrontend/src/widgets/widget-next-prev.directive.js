(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetNextPrev', widgetNextPrev);

    widgetNextPrev.$inject = ['$compile', 'dataservice', '$state'];

    /* @ngInject */
    function widgetNextPrev(compile, dataservice, state) {
        var directive = {
            restrict: 'A',
            link: link
        };
        return directive;

        function link(scope, element, attrs)
        {

            scope.prev = false;
            scope.next = false;

            scope.$watch(function() {
                return dataservice.responseValue
            }, (newValue, oldValue) => {
                if (newValue) {
                    scope.prev = newValue.prev;
                    scope.next = newValue.next;
                }
            });

            element.find('[next]').on('click', () => {
                state.go(state.current.name, {'dataId': scope.next});
            });

            element.find('[prev]').on('click', () => {
                state.go(state.current.name, {'dataId': scope.prev});
            });
        }
    }

})();