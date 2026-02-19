# HeBlogs WordPress 테마

블로그 전용 워드프레스 테마 프로젝트입니다.

## 로컬 개발 환경 설정

이 프로젝트는 Docker를 사용하여 로컬 WordPress 환경을 구성합니다.

### 사전 요구사항

- Docker 및 Docker Compose 설치
- Node.js 및 npm 설치 (SASS 빌드용)

### 빠른 시작

1. **환경 변수 설정 확인**
   ```bash
   # .env 파일이 이미 생성되어 있습니다. 필요시 수정하세요.
   cat .env
   ```

2. **Docker 컨테이너 시작**
   ```bash
   # npm 스크립트 사용
   npm run docker:up
   
   # 또는 직접 docker-compose 사용
   docker-compose up -d
   ```

3. **WordPress 초기 설정**
   - 브라우저에서 `http://localhost:8080` 접속
   - WordPress 설치 화면에서 언어 선택 및 기본 정보 입력
   - 데이터베이스 연결은 자동으로 설정됩니다

4. **테마 활성화**
   - WordPress 관리자 페이지 (`http://localhost:8080/wp-admin`) 로그인
   - 외모 > 테마 메뉴에서 "HeBlogs" 테마 활성화

5. **SASS 빌드 (개발 중)**
   ```bash
   # SASS 파일 감시 모드 (자동 빌드)
   npm run theme:watch
   
   # 또는 수동 빌드
   npm run theme:build
   ```

### Docker 명령어

프로젝트 루트에서 다음 npm 스크립트를 사용할 수 있습니다:

- `npm run docker:up` - 컨테이너 시작 (백그라운드)
- `npm run docker:down` - 컨테이너 중지 및 제거
- `npm run docker:restart` - 컨테이너 재시작
- `npm run docker:stop` - 컨테이너 중지
- `npm run docker:start` - 중지된 컨테이너 시작
- `npm run docker:logs` - 컨테이너 로그 확인
- `npm run docker:ps` - 실행 중인 컨테이너 상태 확인

### 접속 정보

- **WordPress**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - 서버: `db`
  - 사용자명: `wordpress` (기본값)
  - 비밀번호: `wordpress` (기본값)

### 환경 변수

`.env` 파일에서 다음 설정을 변경할 수 있습니다:

- `WP_PORT`: WordPress 포트 (기본: 8080)
- `PHPMYADMIN_PORT`: phpMyAdmin 포트 (기본: 8081)
- `DB_NAME`: 데이터베이스 이름 (기본: wordpress)
- `DB_USER`: 데이터베이스 사용자명 (기본: wordpress)
- `DB_PASSWORD`: 데이터베이스 비밀번호 (기본: wordpress)
- `DB_ROOT_PASSWORD`: MySQL root 비밀번호 (기본: rootpassword)
- `WP_DEBUG`: WordPress 디버그 모드 (기본: 1)

### 개발 워크플로우

1. **테마 파일 수정**
   - `HeBlogs/` 디렉토리의 파일을 수정하면 자동으로 반영됩니다 (볼륨 마운트)
   - 컨테이너 재시작 불필요

2. **스타일 수정**
   ```bash
   # 터미널에서 SASS 감시 모드 실행
   npm run theme:watch
   ```
   - `HeBlogs/assets/style/` 디렉토리의 SCSS 파일 수정
   - 자동으로 `style.css`로 컴파일됨

3. **변경사항 확인**
   - 브라우저에서 `http://localhost:8080` 새로고침
   - WordPress 관리자 페이지에서 테마 설정 확인

### 데이터 저장 위치

- WordPress 파일: `./wordpress-data/`
- MySQL 데이터: `./mysql-data/`
- 테마 파일: `./HeBlogs/` (볼륨 마운트)

### 문제 해결

**컨테이너가 시작되지 않는 경우:**
```bash
# 로그 확인
npm run docker:logs

# 컨테이너 상태 확인
npm run docker:ps
```

**포트 충돌이 발생하는 경우:**
- `.env` 파일에서 `WP_PORT` 또는 `PHPMYADMIN_PORT` 변경

**데이터베이스 연결 오류:**
- 컨테이너가 모두 실행 중인지 확인: `npm run docker:ps`
- `.env` 파일의 데이터베이스 설정 확인

**테마 변경사항이 반영되지 않는 경우:**
- 브라우저 캐시 삭제
- WordPress 관리자에서 테마 재활성화

### 정리

모든 컨테이너와 데이터를 삭제하려면:
```bash
npm run docker:down
# 또는 볼륨까지 삭제하려면
docker-compose down -v
```

주의: `docker-compose down -v` 명령은 모든 데이터베이스 데이터를 삭제합니다.
