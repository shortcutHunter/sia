(function() {
  'use strict';

  angular
    .module('app.nilai')
    .controller('NilaiFormController', NilaiFormController);

  NilaiFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function NilaiFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form Nilai';
    vm.table = 'nilai';

    activate();

    function activate() {
      if (stateParams.dataId) {
        var promises = [getDataDetail()];
        return $q.all(promises).then(function() {
          logger.info('Data loaded');
        });
      }
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();
