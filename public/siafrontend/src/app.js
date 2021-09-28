// liblary
import 'angularjs-datepicker';
import 'angular-base64-upload';

import './app.module';
import './blocks/exception/exception.module';
import './blocks/logger/logger.module';
import './blocks/router/router.module';
import './core/core.module';
import './widgets/widgets.module';
import './modals/modals.module';
import './layout/layout.module';
import './agama/agama.module';
import './pmb/pmb.module';
import './nilai/nilai.module';
import './mata_kuliah/mata_kuliah.module';
import './sequance/sequance.module';
import './tahun_ajaran/tahun_ajaran.module';
import './penerbitan_nim/penerbitan_nim.module';

import './blocks/exception/exception-handler.provider';
import './blocks/exception/exception';
import './blocks/logger/logger';
import './blocks/router/router-helper.provider';

import './core/config';
import './core/constants';
import './core/core.route';
import './core/dataservice';

import './layout/ht-sidebar.directive';
import './layout/shell.controller';
import './layout/sidebar.controller';

import './widgets/widget-pagination.directive';
import './widgets/widget-submit.directive';
import './widgets/widget-back.directive';
import './widgets/widget-tr.directive';
import './widgets/widget-preview.directive';
import './widgets/widget-update-status.directive';
import './widgets/widget-cetak-peserta.directive';

import './modals/modal-preview.directive';

import './agama/agama.controller';
import './agama/agama_form.controller';
import './agama/agama_detail.controller';
import './agama/agama.route';

import './pmb/pmb.controller';
import './pmb/pmb_form.controller';
import './pmb/pmb_detail.controller';
import './pmb/pmb.route';

import './nilai/nilai.controller';
import './nilai/nilai_form.controller';
import './nilai/nilai_detail.controller';
import './nilai/nilai.route';

import './tahun_ajaran/tahun_ajaran.controller';
import './tahun_ajaran/tahun_ajaran_form.controller';
import './tahun_ajaran/tahun_ajaran_detail.controller';
import './tahun_ajaran/tahun_ajaran.route';

import './sequance/sequance.controller';
import './sequance/sequance_form.controller';
import './sequance/sequance_detail.controller';
import './sequance/sequance.route';

import './mata_kuliah/mata_kuliah.controller';
import './mata_kuliah/mata_kuliah_form.controller';
import './mata_kuliah/mata_kuliah_detail.controller';
import './mata_kuliah/mata_kuliah.route';

import './penerbitan_nim/penerbitan_nim.controller';
import './penerbitan_nim/penerbitan_nim.route';