(function() {
  'use strict';

  angular
    .module('app.user')
    .controller('UserDetailController', UserDetailController);

  UserDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope', '$compile', '$element'];
  /* @ngInject */
  function UserDetailController($q, dataservice, logger, stateParams, scope, compile, element) {
    var vm = this;
    vm.title = 'Detail User';
    vm.table = 'user';
    vm.data = {};
    vm.getDataDetail = getDataDetail;
    
    vm.gantiPassword = gantiPassword;

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function gantiPassword() {
      let el = "<modal-ganti-password data='vm.data'></modal-ganti-password>";
      el = compile(el)(scope);
      $(element).append(el);
    }
  }
})();
