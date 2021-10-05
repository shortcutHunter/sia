(function() {
  'use strict';

  angular
    .module('app.tagihan')
    .controller('TagihanDetailController', TagihanDetailController);

  TagihanDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function TagihanDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Tagihan';
    vm.table = 'tagihan';
    vm.data = {};
    vm.option = {};

    activate();

    function activate() {
      var promises = [getDataDetail(), getOption()];
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
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
