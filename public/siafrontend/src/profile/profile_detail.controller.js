(function() {
  'use strict';

  angular
    .module('app.profile')
    .controller('ProfileDetailController', ProfileDetailController);

  ProfileDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope', '$compile', '$element', '$state'];
  /* @ngInject */
  function ProfileDetailController($q, dataservice, logger, stateParams, scope, compile, element, state) {
    var vm = this;
    vm.title = 'Profile';
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
