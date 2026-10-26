<?php
/**
 * Génère un fichier CSV de 200 faux participants répartis en 20 équipes, pour les tests.
 * Usage : php tools/generer_demo.php > tools/demo_participants.csv
 */
$prenoms = ['Awa','Moussa','Fatou','Ibrahim','Aminata','Joseph','Koffi','Mariam','Paul','Adja','Seydou','Aïcha','Jean','Salif','Nadia','Yao','Kadiatou','Olivier','Binta','Arnaud'];
$noms = ['Diallo','Traoré','Koné','Ouédraogo','Mensah','Ndiaye','Kouassi','Camara','Sow','Bamba','Martin','Mbarga','Sylla','Diop','Fofana','Touré','Nguessan','Bernard','Keïta','Owona'];
echo "email;prenom;nom;equipe\n";
for ($i = 1; $i <= 200; $i++) {
    $p = $prenoms[$i % 20];
    $n = $noms[(intdiv($i, 20) + $i) % 20];
    $team = 'Équipe ' . str_pad((string) ((($i - 1) % 20) + 1), 2, '0', STR_PAD_LEFT);
    $email = sprintf('test%03d@demo.worklab.fr', $i);
    echo "$email;$p;$n;$team\n";
}
