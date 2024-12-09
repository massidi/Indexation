
# **Indexation de Fichiers et Recherche avec Nuage de Mots**

Ce projet est un service d'indexation inversée conçu pour analyser et indexer des fichiers texte. Il permet de rechercher des termes spécifiques dans les documents et de créer des nuages de mots basés sur les fréquences d'occurrence des termes. La lemmatization des mots est réalisée grâce à une API développée en Python, qui améliore la précision des termes indexés.

---

## **Fonctionnalités**

- **Indexation Automatique** : Parcours des répertoires pour indexer tous les fichiers texte.
- **Recherche Optimisée** : Recherche rapide grâce à une indexation inversée.
- **Nuage de Mots** : Création de nuages de mots pour visualiser les termes les plus fréquents.
- **Lemmatization via API Python** : Traitement des mots avec un serveur Python personnalisé.
- **Gestion de Stopwords** : Suppression des mots courants pour un traitement plus pertinent.

---

## **Prérequis**

- **Backend :**
    - PHP >= 8.0
    - Symfony >= 5.0
    - Composer
    - MySQL ou un autre SGBD compatible Doctrine
    - Serveur local ou Docker
    - Serveur HTTP (Apache ou Nginx)

- **API Python :**
    - Python >= 3.8
    - Flask ou FastAPI
    - Bibliothèque `spacy` pour la lemmatization.

---

## **Installation**

### **1. Installation du Backend**

1. **Cloner le dépôt :**
   ```bash
   git clone <URL_DU_DEPOT>
   cd <NOM_DU_REPERTOIRE>
   ```

2. **Installer les dépendances :**
   ```bash
   composer install
   ```

3. **Configurer la base de données :**
    - Modifier le fichier `.env` pour indiquer vos paramètres de connexion à la base de données :
      ```
      DATABASE_URL="mysql://user:password@127.0.0.1:3306/nom_base"
      ```

    - Exécuter les migrations :
      ```bash
      php bin/console doctrine:migrations:migrate
      ```

4. **Configurer les paramètres d'application :**
    - Modifier les paramètres dans `config/services.yaml` ou via un fichier `.env.local` selon vos besoins.

5. **Lancer le serveur Symfony :**
   ```bash
   symfony serve
   ```

---

### **2. Installation de l'API Python**

1. **Naviguer dans le répertoire de l'API Python :**
   ```bash
   cd chemin/vers/api-python
   ```

2. **Créer et activer un environnement virtuel :**
   ```bash
   python3 -m venv venv
   source venv/bin/activate
   ```

3. **Installer les dépendances nécessaires :**
   ```bash
   pip install -r requirements.txt
   ```

4. **Lancer le serveur Python :**
   ```bash
   python app.py
   ```

---

## **Utilisation**

### **Indexation des Fichiers**
- Indiquez le répertoire contenant les fichiers texte que vous souhaitez indexer :
  ```php
  $service->parcourirArborescenceEtIndexer('/chemin/vers/dossier');
  ```

### **Recherche d'un Terme**
- Recherchez un mot-clé dans les documents indexés :
  ```php
  $resultats = $service->searchInIndex('mot_cherché');
  ```

### **Afficher les Détails des Documents**
- Liste des documents avec un aperçu des contenus :
  ```php
  $details = $service->allDocumentDetail();
  ```

---

## **Architecture**

### **Principales Entités**
- `Document` : Représente un fichier indexé.
- `Word` : Représente un mot unique avec sa fréquence.
- `InvertedIndex` : Lien entre un mot et un document.

### **Services Principaux**
- **`InvertedIndexService`** : Gère l'indexation, la recherche, et le stockage des données.
- **API Python** : Responsable de la lemmatization des mots pour enrichir l'indexation.

---

## **Aperçu Visuel**

### **Schéma de l'Architecture**
<img src="public/images/admin.png" width="500"/>

### **Maquette de l'Administrateur**
<img src="public/images/admin2.png" width="500"/>

### **Maquette de l'utilisateur**
<img src="public/images/search1.png" width="500"/>

### **Maquette de nuage de mot**
<img src="public/images/search2.png" width="500"/>

---

## **Contributeurs**
- **Votre Nom** - Reddy Massidi semi.

---

## **Licence**
Ce projet est sous licence MIT. Consultez le fichier `LICENSE` pour plus de détails.

---

