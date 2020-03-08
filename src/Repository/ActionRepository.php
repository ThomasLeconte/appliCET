<?php

namespace App\Repository;

use App\Entity\Action;
use App\Repository\PersonnelRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Action|null find($id, $lockMode = null, $lockVersion = null)
 * @method Action|null findOneBy(array $criteria, array $orderBy = null)
 * @method Action[]    findAll()
 * @method Action[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Action::class);
    }

    /**
     * Permet de renvoyer les actions reliés à une personnes en triant par date
     *
     * @param string $numen
     * @return array
     */
    public function findByPersonnelOrderByDate($numen): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Action a
            WHERE a.personnel = :idLdapPersonnel
            ORDER BY a.date ASC'
        )->setParameter('idLdapPersonnel', $numen);

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * Permet de renvoyer les actions reliées à une personne en fonction d'une année donnée
     *
     * @param string $numen
     * @param string $annee
     * @return array
     */
    public function findbyPersonnelAndAnnee($numen, string $annee): array
    {
        $actions = $this->findBy(
            [
                'personnel'=>$numen,
                'annee'=>$annee
            ]
        );
        return $actions;
    }


    /**
     * Permet de retourner l'année d'ouverture d'un compte CET
     *
     * @param string $numen
     * @return void
     */
    public function getDateOuvertureCET(string $numen){
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT annee FROM action a
        WHERE a.personnel = :personnel
        AND a.type_action_id = :typeActionId
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['personnel'=>$numen,
                        'typeActionId' => 1]); //OUVERTURE CET = 1
        $resultat = $stmt->fetchAll();
        return $resultat[0]['annee'];
    }

    /*
    public function findOneBySomeField($value): ?Action
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
