(function() {
  'use strict';

  angular
    .module('app.pmb')
    .controller('PmbDetailController', PmbDetailController);

  PmbDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function PmbDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail PMB';
    vm.table = 'pmb';
    vm.data = {};
    vm.dataservice = dataservice;

    activate();

    function activate() {
      var promises = [getDataDetail(), getOption()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }

  }
})();
