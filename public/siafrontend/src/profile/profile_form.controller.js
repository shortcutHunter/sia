(function() {
  'use strict';

  angular
    .module('app.profile')
    .controller('ProfileFormController', ProfileFormController);

  ProfileFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope'];
  /* @ngInject */
  function ProfileFormController($q, dataservice, logger, stateParams, scope) {
    var vm = this;
    vm.title = 'Form Profile';
    vm.data = dataservice.user;
    vm.table = 'orang';

    activate();

    function activate() {
      var promises = [getOption()];
      return $q.all(promises).then(function() {
        
      });
    }

    scope.$watch(() => dataservice.user, (newVal, oldVal) => {
      if (newVal) {
        vm.data = newVal.orang;
      }
    });

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }

  }
})();
