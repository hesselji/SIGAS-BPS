<?php
use App\Core\Auth;
use App\Core\Csrf;
$me=Auth::user();
$success=flash('flash_success');$error=flash('flash_error');$errors=$_SESSION['errors']??[];unset($_SESSION['errors']);
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="csrf-token" content="<?=e(Csrf::token())?>"><title><?=e(\App\Core\Env::get('APP_NAME','Agenda Surat BPS'))?></title><link rel="stylesheet" href="/assets/css/style.css?v=1"></head><body>
<header class="topbar"><a class="brand" href="/dashboard"><span class="brand-mark">A</span><span><strong>Agenda Surat</strong><small>Prototype Internal • BPS</small></span></a><?php if($me):?><button class="menu-toggle" data-menu-toggle aria-label="Menu">☰</button><nav data-menu><a href="/dashboard">Dashboard</a><a href="/letters">Surat Keluar</a><a class="primary-link" href="/letters/create">+ Generate Nomor</a><?php if(Auth::isAdmin()):?><a href="/admin/users">Pengguna</a><?php endif;?><span class="user-chip"><b><?=e($me['name'])?></b><small><?=e($me['role'])?></small></span><form method="post" action="/logout"><?=csrf_field()?><button class="btn ghost" type="submit">Logout</button></form></nav><?php endif;?></header>
<main class="page-shell"><?php if($success):?><div class="alert success"><?=e($success)?></div><?php endif;?><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
