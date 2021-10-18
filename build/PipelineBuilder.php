<?php


class PipelineBuilder
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
    ' . $jobKey . ': 
      name: Release ' . $image . ':' . $tag . '
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
          run: make verify image=' . $image . ' tag=' . $tag . ' -B
          
        - name: Build Image
          run: make build image=' . $image . ' tag=' . $tag . ' -B
';

        $templateTest = '
        - name: Run SVRUnit Tests
          run: make test image=' . $image . ' tag=' . $tag . ' -B
          
        - name: Store SVRUnit Report
          uses: actions/upload-artifact@v2
          if: always()
          with:
            name: svrunit_report_' . $image . '_' . $tag . '
            retention-days: 3
            path: |
                .reports
                
        - name: Start Image
          run: |
            docker run --rm -p 80:80 --name shop -d dockware/' . $image . ':' . $tag . '
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
               cd tests/cypress && make run6 url=http://localhost
            fi
            if [[ $DW_IMAGE == dev && $SW_VERSION == latest ]]; then
               cd tests/cypress && make run6 url=http://localhost
            fi
            if [[ $SW_VERSION == 6.* ]]; then
               cd tests/cypress && make run6 url=http://localhost
            fi
            if [[ $SW_VERSION == 5.* ]]; then
               cd tests/cypress && make run5 url=http://localhost
            fi
          env:
            DW_IMAGE: ' . $image . '
            SW_VERSION: ' . $tag . '

        - name: Store Cypress Results
          uses: actions/upload-artifact@v2
          if: always()
          with:
            name: cypress_results_' . $image . '_' . $tag . '
            retention-days: 1
            path: |
              Tests/Cypress/cypress/videos
              Tests/Cypress/cypress/screenshots     
';

        $templatePush = '
        - name: Login to Docker Hub
          uses: docker/login-action@v1
          with:
            username: ${{ secrets.DOCKERHUB_USERNAME }}
            password: ${{ secrets.DOCKERHUB_PASSWORD }}
    
        - name: Push Multi-Arch to Docker Hub
          run: make build-and-push-multiarch image=' . $image . ' tag=' . $tag . ' -B';


        return $templateBuild . $templateTest . $templatePush;
    }

}
