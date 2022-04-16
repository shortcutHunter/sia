(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaTagihanController', MahasiswaTagihanController);

  MahasiswaTagihanController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$stateParams', '$compile'];
  /* @ngInject */
  function MahasiswaTagihanController($q, dataservice, logger, scope, stateParams, compile) {
    var vm = this;
    vm.title = 'Tagihan Mahasiswa';
    vm.data = [];

    vm.bayarTagihan = bayarTagihan;

    activate();

    function activate() {
      getData();
    }

    function getData() {
      return dataservice.getUrl('/mahasiswa/tagihan').then(function(response) {
        vm.data = response;
      });
    }

    function bayarTagihan(data) {
      vm.active_data = data;
      let el = "<modal-bayar-tagihan data='vm.active_data'></modal-bayar-tagihan>";
      el = compile(el)(scope);
    }

  }
})();
