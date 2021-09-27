angular.module('siaApp').directive('btnAddModel', btnAddModelFunction);

btnAddModelFunction.$inject = ['$parse'];

function btnAddModelFunction(parse)
{
    return {
        restrict: 'C',
        link: link,
        require: 'ngModel',
    }

    function link(scope, element, attrs, ctrl)
    {
        element.on('click', () => {
            var selectedData = scope.selectedData[attrs.source];
            if (selectedData) {
                var currentValue = ctrl.$modelValue;
                if (!currentValue) {
                    currentValue = [];
                }
                currentValue.push(selectedData);
                ctrl.$setViewValue(currentValue);
                parse(attrs.source).assign(scope, false);
                scope.$apply();
            }
        });
    }
}