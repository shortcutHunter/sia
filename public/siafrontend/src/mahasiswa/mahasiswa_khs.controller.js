(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaKhsController', MahasiswaKhsController);

  MahasiswaKhsController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$stateParams'];
  /* @ngInject */
  function MahasiswaKhsController($q, dataservice, logger, scope, stateParams) {
    var vm = this;
    vm.title = 'KRS Mahasiswa';
    vm.data = [];

    activate();

    function activate() {
      getData();
    }

    function getData() {
      return dataservice.getUrl('/mahasiswa/khs').then(function(response) {
        vm.data = response.data;
        logger.info('Data loaded');
      });
    }

  }
})();
