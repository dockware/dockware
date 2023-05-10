<?php

include __DIR__.'/Constants.php';

class GithubWorkflowBuilder
{
    /**
     * @param $jobKey
     * @param $image
     * @param $tag
     * @return string
     */
    public function buildJob($jobKey, $image, $tag)
    {
        $templateBuild = '
    '.$jobKey.': 
      name: Release '.$image.':'.$tag.'
      runs-on: ubuntu-latest
      continue-on-error: true
      steps:
        - name: Clone Code
          uses: actions/checkout@v2
    
        - name: Install Dependencies
          run: make install
    
        - name: ORCA Generate
          run: make generate -B
    
        - name: Verify Configuration
          run: make verify image='.$image.' tag='.$tag.' -B
          
        - name: Build Image
          run: make build image='.$image.' tag='.$tag.' -B
';

        $templateTest = '
        - name: Run SVRUnit Tests
          run: make test image='.$image.' tag='.$tag.' -B
          
        - name: Store SVRUnit Report
          uses: actions/upload-artifact@v2
          if: always()
          with:
            name: svrunit_report_'.$image.'_'.$tag.'
            retention-days: 3
            path: |
                .reports
        
        - name: Build JUnit Report
          uses: dorny/test-reporter@v1
          if: always()
          with:
            name: SVRUnit Tests
            path: .reports/report.xml
            reporter: jest-junit
                    
        - name: Start Image
          run: |
            docker run --rm -p 80:80 --name shop -d dockware/'.$image.':'.$tag.'
            sleep 30
            docker logs shop

        - name: Install Cypress
          run: cd tests/cypress && make install -B

        - name: Run Cypress Tests
          run: |
            if [[ $DW_IMAGE == flex ]]; then
               cd tests/cypress && make run-flex url=http://localhost
            fi
            if [[ $DW_IMAGE == essentials ]]; then
               cd tests/cypress && make run-essentials url=http://localhost
            fi
            if [[ $DW_IMAGE == play && $SW_VERSION == latest ]]; then
               cd tests/cypress && make run6 url=http://localhost shopware='.Constants::LATEST_SW_VERSION.'
            fi
            if [[ $DW_IMAGE == dev && $SW_VERSION == 6.5.0.0-rc1 ]]; then
               cd tests/cypress && make run6 url=http://localhost shopware=6.5.0.0
            fi
            if [[ $DW_IMAGE == dev && $SW_VERSION == latest ]]; then
               cd tests/cypress && make run6 url=http://localhost shopware='.Constants::LATEST_SW_VERSION.'
            fi
            if [[ $SW_VERSION == 6.* ]]; then
               cd tests/cypress && make run6 url=http://localhost shopware=$SW_VERSION
            fi
            if [[ $SW_VERSION == 5.* ]]; then
               cd tests/cypress && make run5 url=http://localhost shopware=$SW_VERSION
            fi
          env:
            DW_IMAGE: '.$image.'
            SW_VERSION: '.$tag.'

        - name: Store Cypress Results
          uses: actions/upload-artifact@v2
          if: always()
          with:
            name: cypress_results_'.$image.'_'.$tag.'
            retention-days: 1
            path: |
              Tests/Cypress/cypress/videos
              Tests/Cypress/cypress/screenshots     
';

        $templatePush = '
        - name: Set up Docker Buildx
          id: buildx
          uses: docker/setup-buildx-action@v1
        
        - name: Login to Docker Hub
          uses: docker/login-action@v2
          with:
            username: ${{ secrets.DOCKERHUB_USERNAME }}
            password: ${{ secrets.DOCKERHUB_PASSWORD }}
        
        - name: Build amd64
          uses: docker/build-push-action@v2
          with:
            context: ./dist/images/'.$image.'/'.$tag.'
            platforms: linux/amd64
            push: false
            tags: dockware/'.$image.':'.$tag.'
            
        - name: Build arm64
          uses: docker/build-push-action@v2
          with:
            context: ./dist/images/'.$image.'/'.$tag.'
            platforms: linux/arm64/v8
            push: false
            tags: dockware/'.$image.':'.$tag.'
            
        - name: Build and push multiarch
          uses: docker/build-push-action@v2
          with:
            context: ./dist/images/'.$image.'/'.$tag.'
            platforms: linux/amd64,linux/arm64/v8
            push: true
            tags: dockware/'.$image.':'.$tag.'
';


        return $templateBuild.$templateTest.$templatePush;
    }
}
