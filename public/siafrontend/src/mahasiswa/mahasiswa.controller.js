(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaController', MahasiswaController);

  MahasiswaController.$inject = ['$q', 'dataservice', 'logger', '$scope'];
  /* @ngInject */
  function MahasiswaController($q, dataservice, logger, scope) {
    var vm = this;
    vm.title = 'Mahasiswa';
    vm.table = 'mahasiswa';
    vm.page = 0;
    vm.data = [];
    vm.pageData = {};

    activate();

    scope.$watch('vm.page', function(newVal){
      if (newVal != 0) {
        getData();
      }
    });

    function activate() {
      vm.page = 1;
    }

    function getData() {
      return dataservice.getData(vm.table, vm.page).then(function(response) {
        vm.pageData = response;
        vm.data = response.data;
        logger.info('Data loaded');
      });
    }

  }
})();
