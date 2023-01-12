<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function save(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Session[] Returns an array of Session objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findPastSessions(){

        //Récupérer la date du jour 
        //class native php, on met donc un antislash
        $now = new \DateTime();

        return $this->createQueryBuilder('s')
            ->andWhere('s.dateFin < :val')
            -> setParameter('val',$now)
            ->orderBy('s.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();

    }

    public function findFutureSessions(){

        //Récupérer la date du jour 
        //class native php, on met donc un antislash
        $now = new \DateTime();

        return $this->createQueryBuilder('s')
            ->andWhere('s.dateDebut > :val')
            -> setParameter('val',$now)
            ->orderBy('s.dateFin', 'ASC')
            ->getQuery()
            ->getResult();

    }

    public function findProgressSessions(){

        //Récupérer la date du jour 
        //class native php, on met donc un antislash
        $now = new \DateTime();

        return $this->createQueryBuilder('s')
            ->andWhere('s.dateDebut <= :val AND s.dateFin >= :val')
            -> setParameter('val',$now)
            ->orderBy('s.dateFin', 'ASC')
            ->getQuery()
            ->getResult();

    }

    public function findNonInscrits($id)
    {
        //em sert à acceder à doctrine
        $em = $this->getEntityManager();
        //Sert à créér une requête DQL (déjà lié à doctrine de par $em)
        $sub = $em->createQueryBuilder();

        //Requête en deux temps

        // $qb est égal à la création d'une requête + la connexion à Doctrine
        $qb = $sub;
        /*Sélectionner tous les stagiaires d'une session dont l'id est passé en paramètre*/
        //On séléctionne l'objet d'allias 's' avec toutes ses propriétés
        $qb->select('s')
        ->from('App\Entity\Stagiaire', 's')
        // On fait un leftJoin sur la collection sessions de l'entité Stagiaire (car la relation ManyToMany joint Sessions et Stagiaires)
        ->leftJoin('s.sessions', 'se')
        // Où l'id de la session est égal à l'id entré en paramètres
        ->where('se.id = :id');

        // On doit redéfinir sub car ici on fait une autre requête, qui est la suite de la requête
        $sub = $em->createQueryBuilder();
        /* Sélectionner tous les stagiaires qui ne sont pas (NOT IN) le résultat précédent
        => On obtient les stagiaires non inscrits pour une session définie */
        $sub->select('st')
        // On donne l'allias st à l'entité Stagiaire
        ->from('App\Entity\Stagiaire', 'st')
        // Où expr() est un expressionBuilder (sert à utiliser les conditions comme notIn)  les stagiaires dont l'id n'est pas dans la requête précédente 
        ->where($sub->expr()->notIn('st.id', $qb->getDQL()))
        // Requête préparée -> on protège contre l'injection SQL
        ->setParameter('id', $id)
        //On trie sur le nom
        ->orderBy('st.nom');

        //renvoyer le résultat
        //On attribue à $query les valeurs de sub
        $query = $sub->getQuery();
        return $query->getResult();

    }

    public function findProgressSessionsByFormation($id){

        //Récupérer la date du jour 
        //class native php, on met donc un antislash
        $now = new \DateTime();

        $parameters = array(
            'id' => $id,
            'now' => $now
        );

        return $this->createQueryBuilder('s')
            ->where('s.formation = :id')
            ->andWhere('s.dateDebut <= :now AND s.dateFin >= :now')
            -> setParameters($parameters)
            ->orderBy('s.dateFin', 'ASC')
            ->getQuery()
            ->getResult();

    }

    public function findPastSessionsByFormation($id){

        //Récupérer la date du jour 
        //class native php, on met donc un antislash
        $now = new \DateTime();

        $parameters = array(
            'id' => $id,
            'now' => $now
        );

        return $this->createQueryBuilder('s')
            ->where('s.formation = :id')
            ->andWhere('s.dateFin < :now')
            -> setParameters($parameters)
            ->orderBy('s.dateFin', 'ASC')
            ->getQuery()
            ->getResult();

    }

    public function findFutureSessionsByFormation($id){

        //Récupérer la date du jour 
        //class native php, on met donc un antislash
        $now = new \DateTime();

        $parameters = array(
            'id' => $id,
            'now' => $now
        );

        return $this->createQueryBuilder('s')
            ->where('s.formation = :id')
            ->andWhere('s.dateDebut > :now')
            -> setParameters($parameters)
            ->orderBy('s.dateFin', 'ASC')
            ->getQuery()
            ->getResult();

    }

}
