<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify Admin — <?= isset($category)?'Modifier':'Créer' ?> une Catégorie</title>
<?php require BASE_PATH . '/View/shared/_styles_admin.php'; ?>
<style>
.form-wrap{max-width:560px;margin:0 auto;}
.form-card{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
.form-header{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;padding:24px 28px;display:flex;align-items:center;gap:14px;}
.form-header-icon{font-size:2rem;}
.form-header h1{font-size:1.3rem;font-weight:700;}
.form-header p{font-size:.84rem;opacity:.8;margin-top:2px;}
.form-body{padding:28px;}
.form-group{display:flex;flex-direction:column;gap:5px;margin-bottom:18px;}
label{font-size:.82rem;font-weight:600;color:var(--text);}
label .req{color:var(--danger);}
input[type="text"],textarea{width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:.88rem;font-family:inherit;color:var(--text);outline:none;transition:.2s;background:#fff;}
input:focus,textarea:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(108,99,255,.08);}
textarea{resize:vertical;min-height:100px;}
.field-error{font-size:.76rem;color:var(--danger);margin-top:2px;display:none;}
.field-error.show{display:block;}
input.invalid,textarea.invalid{border-color:var(--danger);}
.form-footer{display:flex;justify-content:flex-end;gap:10px;padding-top:16px;border-top:1px solid var(--border);}
</style>
</head>
<body>
<div class="layout">
  <?php $activeModule = 'categories'; require BASE_PATH . '/View/shared/_sidebar.php'; ?>
  <main class="main">
    <div class="topbar">
      <h1 class="page-title"><?= isset($category)?'Modifier':'Créer' ?> une <span>Catégorie</span></h1>
      <div class="topbar-actions">
        <a href="index.php?module=categories&action=list" class="btn btn-ghost">← Retour</a>
      </div>
    </div>
    <div class="page-content">
      <div class="form-wrap">
        <div class="form-card">
          <div class="form-header">
            <span class="form-header-icon">📁</span>
            <div>
              <h1><?= isset($category)?'Modifier la catégorie':'Nouvelle catégorie' ?></h1>
              <p><?= isset($category)?'Mettez à jour les informations.':'Remplissez le formulaire.' ?></p>
            </div>
          </div>
          <div class="form-body">
            <form id="catForm" method="POST" action="index.php?module=categories&action=<?= isset($category)?'update':'store' ?>" novalidate>
              <?php if(isset($category)): ?><input type="hidden" name="id" value="<?= $category['id'] ?>"><?php endif; ?>
              <div class="form-group">
                <label for="name">Nom <span class="req">*</span></label>
                <input type="text" id="name" name="name" placeholder="Ex: Développement Web" value="<?= htmlspecialchars($category['name']??'') ?>">
                <span class="field-error" id="err-name">Le nom est obligatoire (2 à 100 caractères).</span>
              </div>
              <div class="form-group">
                <label for="description">Description (optionnel)</label>
                <textarea id="description" name="description" placeholder="Décrivez cette catégorie…"><?= htmlspecialchars($category['description']??'') ?></textarea>
              </div>
              <div class="form-footer">
                <a href="index.php?module=categories&action=list" class="btn btn-ghost">Annuler</a>
                <button type="submit" class="btn btn-primary"><?= isset($category)?'💾 Mettre à jour':'✅ Créer la catégorie' ?></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
<script>
(function(){
  function vf(id,err,fn){var el=document.getElementById(id);var v=el.value.trim();if(!fn(v)){el.classList.add('invalid');document.getElementById(err).classList.add('show');return false;}el.classList.remove('invalid');document.getElementById(err).classList.remove('show');return true;}
  var el=document.getElementById('name');
  ['blur','input'].forEach(function(ev){el.addEventListener(ev,function(){vf('name','err-name',function(v){return v.length>=2&&v.length<=100;});});});
  document.getElementById('catForm').addEventListener('submit',function(e){if(!vf('name','err-name',function(v){return v.length>=2&&v.length<=100;})){e.preventDefault();}});
})();
</script>
</body>
</html>
