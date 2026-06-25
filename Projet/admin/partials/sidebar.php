<?php
$currentPage = $currentPage ?? '';
?>
<aside class="sidebar">
    <div class="logo">
        <img src="../professeur/enjah.png" alt="logo">
        <span class="brand-name">Admin</span>
    </div>
    <nav><ul>
        <li<?php if ($currentPage === 'dashboard') echo ' class="active"'; ?>><a href="index.php">Tableau de bord</a></li>
        <li<?php if ($currentPage === 'students') echo ' class="active"'; ?>><a href="students.php">Étudiants</a></li>
        <li<?php if ($currentPage === 'professors') echo ' class="active"'; ?>><a href="professors.php">Professeurs</a></li>
        <li<?php if ($currentPage === 'payments') echo ' class="active"'; ?>><a href="payments.php">Paiements</a></li>
        <li<?php if ($currentPage === 'chat') echo ' class="active"'; ?>><a href="chat.php">Support Chat<?php if (!empty($unreadMessages) && $unreadMessages > 0): ?> <span class="badge"><?php echo $unreadMessages; ?></span><?php endif; ?></a></li>
        <li<?php if ($currentPage === 'admins') echo ' class="active"'; ?>><a href="admins.php">Admins</a></li>
        <li><a href="logout.php">Déconnexion</a></li>
    </ul></nav>
</aside>
