angular.module('siaApp').directive('autocomplete', buttonSaveFunction);

buttonSaveFunction.$inject = ['appService'];

function buttonSaveFunction(appService)
{
    return {
        restrict: 'E',
        link: link,
        require: 'ngModel',
        'template': `
            <div class="easy-complete-container">
                <input type="text" class="form-control form-control-sm">
            </div>
        `,
        replace: true
    }

    function link(scope, element, attrs, ctrl)
    {
        var options = {
            url: function(phrase) {
                return `${attrs.remoteUrl}${phrase}&page=1`;
            },
            listLocation: "data",
            getValue: "nama",
            list: {
                onSelectItemEvent: function()
                {
                    var data = $(element).find('input').getSelectedItemData();
                    if(!scope.selectedData){
                        scope.selectedData = {};
                    }
                    scope.selectedData[attrs.ngModel] = data;
                    ctrl.$setViewValue(data.id);
                }
            }
        };

        $(element).find('input').easyAutocomplete(options);

        scope.$watch(() => ctrl.$modelValue, function(newVal, oldVal){
            if (newVal == false) {
                $(element).find('input').val('');
            }
        });

    }
}