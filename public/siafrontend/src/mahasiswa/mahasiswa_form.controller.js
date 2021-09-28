(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaFormController', MahasiswaFormController);

  MahasiswaFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function MahasiswaFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form Mahasiswa';
    vm.table = 'mahasiswa';

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
